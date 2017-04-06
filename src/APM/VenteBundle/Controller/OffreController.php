<?php

namespace APM\VenteBundle\Controller;

use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Boutique;
use APM\VenteBundle\Entity\Categorie;
use APM\VenteBundle\Entity\Offre;
use APM\VenteBundle\Factory\TradeFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Offre controller.
 *
 */
class OffreController extends Controller
{

    /**
     * Liste toutes les offres de l'utilisateur
     * @param Boutique $boutique
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Boutique $boutique =null)
    {
        $this->listAndShowSecurity($boutique);
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $offres = $user->getOffres();
        return $this->render('APMVenteBundle:offre:index.html.twig', array(
            'offres' => $offres,
            'boutique' => $boutique
        ));
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
        $form = $this->createForm('APM\VenteBundle\Form\OffreType', $offre);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->createSecurity($form->get('boutique')->getData(),$form->get('categorie')->getData());
            $offre->setVendeur($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($offre);
            $em->flush();

            return $this->redirectToRoute('apm_vente_offre_show', array('id' => $offre->getId()));
        }

        return $this->render('APMVenteBundle:offre:new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Offre entity.
     * @param Offre $offre
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Offre $offre)
    {
        $this->listAndShowSecurity($offre->getBoutique());
        $deleteForm = $this->createDeleteForm($offre);

        return $this->render('APMVenteBundle:offre:show.html.twig', array(
            'offre' => $offre,
            'delete_form' => $deleteForm->createView(),
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

            return $this->redirectToRoute('apm_vente_offre_show', array('id' => $offre->getId()));
        }

        return $this->render('APMVenteBundle:offre:edit.html.twig', array(
            'offre' => $offre,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
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


    /**
     * @param Boutique $boutique
     */
    private function listAndShowSecurity($boutique){
        //-----------------------------------security-------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED'))  {
            throw $this->createAccessDeniedException();
        }
        if($boutique) {
            $user = $this->getUser();
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
            if ($user !== $gerant&&$user !==$proprietaire) {
                throw $this->createAccessDeniedException();
            }
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @param Categorie $categorie
     * @param Boutique $boutique
     */
    private function createSecurity( $boutique =null, $categorie = null){
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();}
        /* ensure that the user is logged in  # granted even through remembering cookies
        *  and that the one is the owner
        */
        //Autoriser l'accès à la boutique uniquement au gerant et au proprietaire
        if($boutique) {
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
     * @param Offre $offre
     */
    private function editAndDeleteSecurity($offre){
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();}
        /* ensure that the user is logged in  # granted even through remembering cookies
        *  and that the one is the owner
        */
        $boutique = $offre->getBoutique();
        if($boutique) {
            $user = $this->getUser();
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
            if ($user !== $gerant && $user !== $proprietaire) {
                throw $this->createAccessDeniedException();
            }
        }
        //----------------------------------------------------------------------------------------

    }
}
