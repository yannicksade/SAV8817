<?php

namespace APM\VenteBundle\Controller;

use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Offre;
use APM\VenteBundle\Entity\Rabais_offre;
use APM\VenteBundle\Factory\TradeFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Rabais_offre controller.
 *
 */
class Rabais_offreController extends Controller
{

    /**
     * Le vendeur crée des rabais pour un utilisateur donné
     * Liste les rabais créé par le vendeur
     *
     */
    public function indexAction()
    {
        $this->listAndShowSecurity();
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $rabais_offres = $user->getRabais();
        return $this->render('APMVenteBundle:rabais_offre:index.html.twig', array(
            'rabais_offres' => $rabais_offres,
        ));
    }

    /**
     * Creates a new Rabais_offre entity.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $this->createSecurity();
        /** @var Rabais_offre $rabais */
        $rabais = TradeFactory::getTradeProvider('rabais');
        $form = $this->createForm('APM\VenteBundle\Form\Rabais_offreType', $rabais);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $offre = $form->get('offre');
            $this->createSecurity($offre->getData());
            $rabais->setVendeur($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($rabais);
            $em->flush();

            return $this->redirectToRoute('apm_vente_rabais_offre_show', array('id' => $rabais->getId()));
        }

        return $this->render('APMVenteBundle:rabais_offre:new.html.twig', array(
            'rabais_offre' => $rabais,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Rabais_offre entity.
     * @param Rabais_offre $rabais_offre
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Rabais_offre $rabais_offre)
    {
        $this->listAndShowSecurity();
        $deleteForm = $this->createDeleteForm($rabais_offre);
        return $this->render('APMVenteBundle:rabais_offre:show.html.twig', array(
            'rabais_offre' => $rabais_offre,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Rabais_offre entity.
     *
     * @param Rabais_offre $rabais_offre The Rabais_offre entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Rabais_offre $rabais_offre)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_vente_rabais_offre_delete', array('id' => $rabais_offre->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Rabais_offre entity.
     * @param Request $request
     * @param Rabais_offre $rabais_offre
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Rabais_offre $rabais_offre)
    {
        $this->editAndDeleteSecurity($rabais_offre);
        $deleteForm = $this->createDeleteForm($rabais_offre);
        $editForm = $this->createForm('APM\VenteBundle\Form\Rabais_offreType', $rabais_offre);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->editAndDeleteSecurity($rabais_offre);
            $em = $this->getDoctrine()->getManager();
            $em->persist($rabais_offre);
            $em->flush();

            return $this->redirectToRoute('apm_vente_rabais_offre_show', array('id' => $rabais_offre->getId()));
        }

        return $this->render('APMVenteBundle:rabais_offre:edit.html.twig', array(
            'rabais_offre' => $rabais_offre,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Rabais_offre entity.
     * @param Request $request
     * @param Rabais_offre $rabais_offre
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Rabais_offre $rabais_offre)
    {
        $this->editAndDeleteSecurity($rabais_offre);
        $form = $this->createDeleteForm($rabais_offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->editAndDeleteSecurity($rabais_offre);
            $em = $this->getDoctrine()->getManager();
            $em->remove($rabais_offre);
            $em->flush();
        }

        return $this->redirectToRoute('apm_vente_rabais_offre_index');
    }

    public function deleteFromListAction(Rabais_offre $rabais_offre)
    {
        $this->editAndDeleteSecurity($rabais_offre);

        $em = $this->getDoctrine()->getManager();
        $em->remove($rabais_offre);
        $em->flush();

        return $this->redirectToRoute('apm_vente_rabais_offre_index');
    }


    private function listAndShowSecurity(){
        //-----------------------------------security-------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED'))  {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }


    /**
     * @param Offre $offre
     */
    private function createSecurity($offre =null){
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();}

        if($offre) { /// N'autorise que le vendeur, le gerant ou le proprietaire à pouvoir  faire des rabais
            $user = $this->getUser();
            $boutique = $offre->getBoutique();
            $gerant = null;
            $proprietaire = null;
            if($boutique) {
                $gerant = $boutique->getGerant();
                $proprietaire = $boutique->getProprietaire();
            }
            $vendeur = $offre->getVendeur();
            if ($user !== $gerant && $user !== $proprietaire && $user !== $vendeur) {
                throw $this->createAccessDeniedException();
            }
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @param Rabais_offre $rabais
     */
    private function editAndDeleteSecurity($rabais){
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        /* ensure that the user is logged in  # granted even through remembering cookies
        *  and that the one is the owner
        */
        $user = $this->getUser();
        $vendeur = $rabais->getVendeur();
        if ((!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) || ($vendeur!== $user)) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------

    }
}
