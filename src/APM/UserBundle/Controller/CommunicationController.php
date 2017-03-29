<?php

namespace APM\UserBundle\Controller;

use APM\UserBundle\Entity\Communication;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Communication controller.
 *
 */
class CommunicationController extends Controller
{
    /**
     * Lists all Communication entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $listCommunications = $em->getRepository('APMUserBundle:Communication')->findAll();

        return $this->render('APMUserBundle:communication:index.html.twig', array(
            'communications' => $listCommunications
        ));
    }

    /**
     * Creates a new Communication entity.
     *
     */
    public function newAction(Request $request)
    {
        $communication = new Communication();
        $form = $this->createForm('APM\UserBundle\Form\CommunicationType', $communication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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

    /**
     * Finds and displays a Communication entity.
     *
     */
    public function showAction(Communication $communication)
    {
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
     *
     */
    public function editAction(Request $request, Communication $communication)
    {
        $deleteForm = $this->createDeleteForm($communication);
        $editForm = $this->createForm('APM\UserBundle\Form\CommunicationType', $communication);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
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
     * Deletes a Communication entity.
     *
     */
    public function deleteAction(Request $request, Communication $communication)
    {
        $form = $this->createDeleteForm($communication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($communication);
            $em->flush();
        }

        return $this->redirectToRoute('apm_user_communication_index');
    }

    public function deleteFromListAction(Communication $object)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($object);
        $em->flush();

        return $this->redirectToRoute('apm_user_communication_index');
    }
}
