<?php

namespace APM\VenteBundle\Controller;

use APM\CoreBundle\Form\Type\FilterFormType;
use Doctrine\Common\Collections\Collection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use APM\VenteBundle\Entity\Boutique;
use APM\VenteBundle\Entity\Categorie;
use APM\VenteBundle\Entity\Offre;
use APM\VenteBundle\Factory\TradeFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Offre controller.
 *
 */
class OffreController extends Controller
{
    private $value;

    /**
     * @ParamConverter("categorie", options={"mapping": {"categorie_id":"id"}})
     * Liste les offres de la boutique ou du vendeur
     * @param Request $request
     * @param Boutique $boutique
     * @param Categorie $categorie
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, Boutique $boutique = null, Categorie $categorie = null)
    {
        $vendeur = null;
        if (isset($boutique)) {
            $this->listAndShowSecurity($boutique);
            if ($categorie) {
                $offres = $categorie->getOffres();
            } else {
                $offres = $boutique->getOffres();
            }
        } else {

            $this->listAndShowSecurity();
            $user = $this->getUser();
            /** @var Collection $offres */
            $offres = $user->getOffres();
            /** @var Offre $anOffer */
            $anOffer = $offres->offsetGet(0);
            if ($anOffer) $vendeur = $anOffer->getVendeur();
        }
        //-------------------------------------------------------------
        $filter = $this->createForm(FilterFormType::class);
        $filter->handleRequest($request);
        if ($filter->isSubmitted() && $filter->isValid()) {
            $this->value = $filter->get('filter')->getData();
            $offres = $offres->filter(function ($offre) {//filtrage
                /** @var Offre $offre */
                return $offre->getDesignation() == $this->value;
            });
        }
        $offres = $offres->slice(0, 10); // slice de pagination
        //--------------------------------------------------------------
        return $this->render('APMVenteBundle:offre:index.html.twig', array(
            'filter' => $filter->createView(),
            'offres' => $offres,
            'boutique' => $boutique,
            'categorie' => $categorie,
            'vendeur' => $vendeur,
            'url_image' => $this->get('apm_core.packages_maker')->getPackages()->getUrl('/', 'img'),
        ));
    }

    /**
     * @param Boutique $boutique
     */
    private function listAndShowSecurity($boutique = null)
    {
        //-----------------------------------security-------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        if ($boutique) {
            $user = $this->getUser();
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
            if ($user !== $gerant && $user !== $proprietaire) {
                throw $this->createAccessDeniedException();
            }
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $this->createSecurity();
        /** @var Offre $offre */
        $offre = TradeFactory::getTradeProvider('offre');
        $offre->setVendeur($this->getUser());
        $form = $this->createForm('APM\VenteBundle\Form\OffreType', $offre);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->createSecurity($offre->getBoutique(), $offre->getCategorie());
            $em = $this->getDoctrine()->getManager();
            $em->persist($offre);
            $em->flush();
            $this->get('apm_core.crop_image')->liipImageResolver($offre->getImage());//resouds tout en créant l'image
            //---
            $dist = dirname(__DIR__, 4);
            $file = $dist . '/web/' . $this->getParameter('images_url') . '/' . $offre->getImage();
            if (file_exists($file)) {
                return $this->redirectToRoute('apm_vente_offre_show-image', array('id' => $offre->getId()));
            } else {
                return $this->redirectToRoute('apm_vente_offre_show', array('id' => $offre->getId()));
            }
            //---
        }
        return $this->render('APMVenteBundle:offre:new.html.twig', array(
            'form' => $form->createView(),
            'offre' => $offre,
            'url_image' => $this->get('apm_core.packages_maker')->getPackages()->getUrl('/', 'img'),
        ));
    }

    /**
     * @param Categorie $categorie
     * @param Boutique $boutique
     */
    private function createSecurity($boutique = null, $categorie = null)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        /* ensure that the user is logged in  # granted even through remembering cookies
        *  and that the one is the owner
        */
        //Autoriser l'accès à la boutique uniquement au gerant et au proprietaire
        if ($boutique) {
            $user = $this->getUser();
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
            if ($user !== $gerant && $user !== $proprietaire) {
                throw $this->createAccessDeniedException();
            }
            if ($categorie) {//Inserer l'offre uniquement dans la meme boutique que la categorie
                $currentBoutique = $categorie->getBoutique();
                if ($currentBoutique !== $boutique) {
                    throw $this->createAccessDeniedException();
                }
            }
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * Tout utilisateur AVM peut voir une offre
     * @param Request $request
     * @param Offre $offre
     * @return Response
     */
    public function showImageAction(Request $request, Offre $offre)
    {
        $this->listAndShowSecurity();
        $form = $this->createCrobForm($offre);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('apm_core.crop_image')->setCropParameters(intval($_POST['x']), intval($_POST['y']), intval($_POST['w']), intval($_POST['h']), $offre->getImage(), $offre);

            return $this->redirectToRoute('apm_vente_offre_show', array('id' => $offre->getId()));
        }

        return $this->render('APMVenteBundle:offre:image.html.twig', array(
            'offre' => $offre,
            'crop_form' => $form->createView(),
            'url_image' => $this->get('apm_core.packages_maker')->getPackages()->getUrl('/', 'resolve_img'),
        ));
    }

    private function createCrobForm(Offre $offre)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_vente_offre_show-image', array('id' => $offre->getId())))
            ->setMethod('POST')
            ->getForm();
    }

    /**
     * Tout utilisateur AVM peut voir une offre
     * @param Offre $offre
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Offre $offre)
    {
        $this->listAndShowSecurity();
        $deleteForm = $this->createDeleteForm($offre);

        return $this->render('APMVenteBundle:offre:show.html.twig', array(
            'offre' => $offre,
            'delete_form' => $deleteForm->createView(),
            'url_image' => $this->get('apm_core.packages_maker')->getPackages()->getUrl('/', 'resolve_img'),
        ));
    }

    /**
     * Creates a form to delete a Offre entity.
     *
     * @param Offre $offre The Offre entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Offre $offre)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_vente_offre_delete', array('id' => $offre->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Offre entity.
     * @param Request $request
     * @param Offre $offre
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Offre $offre)
    {
        $this->editAndDeleteSecurity($offre);
        $deleteForm = $this->createDeleteForm($offre);
        $editForm = $this->createForm('APM\VenteBundle\Form\OffreType', $offre);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->createSecurity();
            $em = $this->getDoctrine()->getManager();
            $em->persist($offre);
            $em->flush();

            //---
            $dist = dirname(__DIR__, 4);
            $file = $dist . '/web/' . $this->getParameter('images_url') . '/' . $offre->getImage();
            if (file_exists($file)) {
                return $this->redirectToRoute('apm_vente_offre_show-image', array('id' => $offre->getId()));
            } else {
                return $this->redirectToRoute('apm_vente_offre_show', array('id' => $offre->getId()));
            }
            //---
        }

        return $this->render('APMVenteBundle:offre:edit.html.twig', array(
            'offre' => $offre,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'url_image' => $this->get('apm_core.packages_maker')->getPackages()->getUrl('/', 'resolve_img'),
        ));
    }

    /**
     * @param Offre $offre
     */
    private function editAndDeleteSecurity($offre)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        /* ensure that the user is logged in  # granted even through remembering cookies
        *  and that the one is the owner
        */
        $boutique = $offre->getBoutique();
        if ($boutique) {
            $user = $this->getUser();
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
            if ($user !== $gerant && $user !== $proprietaire) {
                throw $this->createAccessDeniedException();
            }
        }
        //----------------------------------------------------------------------------------------

    }

    /**
     * Deletes a Offre entity.
     * @param Request $request
     * @param Offre $offre
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Offre $offre)
    {
        $this->editAndDeleteSecurity($offre);
        $form = $this->createDeleteForm($offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->createSecurity();
            $em = $this->getDoctrine()->getManager();
            $em->remove($offre);
            $em->flush();
        }

        return $this->redirectToRoute('apm_vente_offre_index');
    }

    public function deleteFromListAction(Offre $offre)
    {
        $this->editAndDeleteSecurity($offre);

        $em = $this->getDoctrine()->getManager();
        $em->remove($offre);
        $em->flush();

        return $this->redirectToRoute('apm_vente_offre_index');
    }
}
