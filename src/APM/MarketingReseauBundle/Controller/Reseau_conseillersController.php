<?php

namespace APM\MarketingReseauBundle\Controller;

use APM\MarketingDistribueBundle\Entity\Conseiller;
use APM\MarketingReseauBundle\Entity\Reseau_conseillers;
use APM\MarketingReseauBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Reseau_conseillers controller.
 *
 */
class Reseau_conseillersController extends Controller
{
    // liste les 2max  réseaux du conseiller A2
    public function indexAction()
    {
        $this->listAndShowSecurity();
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $reseau_conseillers = $user->getProfileConseiller()->getReseau();
        return $this->render('APMMarketingReseauBundle:reseau_conseillers:index.html.twig', array(
            'reseau_conseillers' => $reseau_conseillers,
        ));
    }

   //Verifie si l'utilisateur est un conseiller
    public function newAction(Request $request)
    {
        $this->createSecurity();

        /** @var Reseau_conseillers $reseau_conseiller */
        $reseau_conseiller = TradeFactory::getTradeProvider("reseau_conseillers");
        $form = $this->createForm('APM\MarketingReseauBundle\Form\Reseau_conseillersType', $reseau_conseiller);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->createSecurity();
            /** @var Utilisateur_avm $user */
            $user = $this->getUser();
            $conseiller = $user->getProfileConseiller();
            $reseau_conseiller->setConseillerProprietaire($conseiller);
            $em = $this->getDoctrine()->getManager();
            $em->persist($reseau_conseiller);
            $em->flush();

            return $this->redirectToRoute('apm_marketing_reseau_conseillers_show', array('id' => $reseau_conseiller->getId()));
        }

        return $this->render('APMMarketingReseauBundle:reseau_conseillers:new.html.twig', array(
            'reseau_conseiller' => $reseau_conseiller,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Reseau_conseillers entity.
     * @param Reseau_conseillers $reseau_conseiller
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Reseau_conseillers $reseau_conseiller)
    {
        $this->listAndShowSecurity();
        $deleteForm = $this->createDeleteForm($reseau_conseiller);

        return $this->render('APMMarketingReseauBundle:reseau_conseillers:show.html.twig', array(
            'reseau_conseiller' => $reseau_conseiller,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Reseau_conseillers entity.
     *
     * @param Reseau_conseillers $reseau_conseiller The Reseau_conseillers entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Reseau_conseillers $reseau_conseiller)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_marketing_reseau_conseillers_delete', array('id' => $reseau_conseiller->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Reseau_conseillers entity.
     * @param Request $request
     * @param Reseau_conseillers $reseau_conseiller
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Reseau_conseillers $reseau_conseiller)
    {
        $this->editAndDeleteSecurity($reseau_conseiller);
        $deleteForm = $this->createDeleteForm($reseau_conseiller);
        $editForm = $this->createForm('APM\MarketingReseauBundle\Form\Reseau_conseillersType', $reseau_conseiller);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($reseau_conseiller);
            $em->flush();

            return $this->redirectToRoute('apm_marketing_reseau_conseillers_show', array('id' => $reseau_conseiller->getId()));
        }

        return $this->render('APMMarketingReseauBundle:reseau_conseillers:edit.html.twig', array(
            'reseau_conseiller' => $reseau_conseiller,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Reseau_conseillers entity.
     * @param Request $request
     * @param Reseau_conseillers $reseau_conseiller
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Reseau_conseillers $reseau_conseiller)
    {
        $this->editAndDeleteSecurity($reseau_conseiller);

        $form = $this->createDeleteForm($reseau_conseiller);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($reseau_conseiller);
            $em->flush();
        }

        return $this->redirectToRoute('apm_marketing_reseau_conseillers_index');
    }

    public function deleteFromListAction(Reseau_conseillers $reseau_conseillers)
    {
       $this->editAndDeleteSecurity($reseau_conseillers);
        $em = $this->getDoctrine()->getManager();
        $em->remove($reseau_conseillers);
        $em->flush();

        return $this->redirectToRoute('apm_marketing_reseau_conseillers_index');
    }

    /**
     * @param Reseau_conseillers $reseau_conseiller
     */
    private function editAndDeleteSecurity($reseau_conseiller){
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_CONSEILLER_A2', null, 'Unable to access this page!');
        /* ensure that the user is logged in
        */
        $user = $this->getUser();
        if ((!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) || $user !== $reseau_conseiller->getConseillerProprietaire()->getUtilisateur()) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    private function createSecurity(){
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a CONSEILLER role
        $this->denyAccessUnlessGranted('ROLE_CONSEILLER_A2', null, 'Unable to access this page!');
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $conseiller = $user->getProfileConseiller();
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') || null === $conseiller) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }
    private function listAndShowSecurity(){
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a CONSEILLER role
        $this->denyAccessUnlessGranted('ROLE_CONSEILLER_A2', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }
}
