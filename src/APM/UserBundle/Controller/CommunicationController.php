<?php

namespace APM\UserBundle\Controller;

use APM\UserBundle\Entity\Communication;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\UserBundle\Factory\TradeFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Communication controller.
 *
 */
class CommunicationController extends Controller
{
    /**
     * Lister les communication reçues et envoyées par un utilisateur
     *
     */
    public function indexAction()
    {
        $this->listAndShowSecurity();
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $communicationsSent = $user->getEmetteurCommunications();
        $communicationsReceived = $user->getRecepteurCommunications();
        return $this->render('APMUserBundle:communication:index.html.twig', array(
            'communicationsSent' => $communicationsSent,
            'communicationsReceived' => $communicationsReceived,
        ));
    }

    private function listAndShowSecurity()
    {
        //-----------------------------------security-------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * L'Emetteur Crée et soumet un model de communication
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $this->createSecurity();
        /** @var Communication $communication */
        $communication = TradeFactory::getTradeProvider("communication");
        $form = $this->createForm('APM\UserBundle\Form\CommunicationType', $communication);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->createSecurity();
            $communication->setEmetteur($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($communication);
            $em->flush();

            return $this->redirectToRoute('apm_user_communication_show', array('id' => $communication->getId()));
        }

        return $this->render('APMUserBundle:communication:new.html.twig', array(
            'communication' => $communication,
            'form' => $form->createView(),
        ));
    }

    private function createSecurity()
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');

        /* ensure that the user is logged in */
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * Finds and displays a Communication entity.
     * @param Communication $communication
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Communication $communication)
    {
        $this->listAndShowSecurity();
        $deleteForm = $this->createDeleteForm($communication);

        return $this->render('APMUserBundle:communication:show.html.twig', array(
            'communication' => $communication,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Communication entity.
     *
     * @param Communication $communication The Communication entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Communication $communication)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_user_communication_delete', array('id' => $communication->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Communication entity.
     * @param Request $request
     * @param Communication $communication
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Communication $communication)
    {
        $this->editAndDeleteSecurity($communication);
        $deleteForm = $this->createDeleteForm($communication);
        $editForm = $this->createForm('APM\UserBundle\Form\CommunicationType', $communication);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->editAndDeleteSecurity($communication);
            $em = $this->getDoctrine()->getManager();
            $em->persist($communication);
            $em->flush();

            return $this->redirectToRoute('apm_user_communication_show', array('id' => $communication->getId()));
        }

        return $this->render('APMUserBundle:communication:edit.html.twig', array(
            'communication' => $communication,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @param Communication $communication
     */
    private function editAndDeleteSecurity($communication)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');

        /* ensure that the user is logged in
        *  and that the one is the owner
        */
        $user = $this->getUser();
        if ((!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) || ($communication->getEmetteur() !== $user)) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------

    }

    /**
     * Deletes a Communication entity.
     * @param Request $request
     * @param Communication $communication
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Communication $communication)
    {
        $this->editAndDeleteSecurity($communication);
        $form = $this->createDeleteForm($communication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->editAndDeleteSecurity($communication);
            $em = $this->getDoctrine()->getManager();
            $em->remove($communication);
            $em->flush();
        }

        return $this->redirectToRoute('apm_user_communication_index');
    }

    public function deleteFromListAction(Communication $communication)
    {
        $this->editAndDeleteSecurity($communication);
        $em = $this->getDoctrine()->getManager();
        $em->remove($communication);
        $em->flush();

        return $this->redirectToRoute('apm_user_communication_index');
    }

}
