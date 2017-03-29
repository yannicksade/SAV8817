<?php

namespace APM\TransportBundle\Controller;

use APM\TransportBundle\Entity\Profile_transporteur;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


/**
 * Profile_transporteur controller.
 *
 */
class Profile_transporteurController extends Controller
{
    /**
     * Lists all Profile_transporteur entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $profile_transporteurs = $em->getRepository('APMTransportBundle:Profile_transporteur')->findAll();

        return $this->render('APMTransportBundle:profile_transporteur:index.html.twig', array(
            'profile_transporteurs' => $profile_transporteurs,
        ));
    }

    /**
     * Creates a new Profile_transporteur entity.
     *
     */
    public function newAction(Request $request)
    {
        $profile_transporteur = new Profile_transporteur();
        $form = $this->createForm('APM\TransportBundle\Form\Profile_transporteurType', $profile_transporteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($profile_transporteur);
            $em->flush();

            return $this->redirectToRoute('apm_transport_transporteur_show', array('id' => $profile_transporteur->getId()));
        }

        return $this->render('APMTransportBundle:profile_transporteur:new.html.twig', array(
            'profile_transporteur' => $profile_transporteur,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Profile_transporteur entity.
     *
     */
    public function showAction(Profile_transporteur $profile_transporteur)
    {
        $deleteForm = $this->createDeleteForm($profile_transporteur);

        return $this->render('APMTransportBundle:profile_transporteur:show.html.twig', array(
            'profile_transporteur' => $profile_transporteur,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Profile_transporteur entity.
     *
     * @param Profile_transporteur $profile_transporteur The Profile_transporteur entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Profile_transporteur $profile_transporteur)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_transport_transporteur_delete', array('id' => $profile_transporteur->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Profile_transporteur entity.
     *
     */
    public function editAction(Request $request, Profile_transporteur $profile_transporteur)
    {
        $deleteForm = $this->createDeleteForm($profile_transporteur);
        $editForm = $this->createForm('APM\TransportBundle\Form\Profile_transporteurType', $profile_transporteur);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($profile_transporteur);
            $em->flush();

            return $this->redirectToRoute('apm_transport_transporteur_show', array('id' => $profile_transporteur->getId()));
        }

        return $this->render('APMTransportBundle:profile_transporteur:edit.html.twig', array(
            'profile_transporteur' => $profile_transporteur,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Profile_transporteur entity.
     *
     */
    public function deleteAction(Request $request, Profile_transporteur $profile_transporteur)
    {
        $form = $this->createDeleteForm($profile_transporteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($profile_transporteur);
            $em->flush();
        }

        return $this->redirectToRoute('apm_transport_transporteur_index');
    }

    public function deleteFromListAction(Profile_transporteur $object)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($object);
        $em->flush();

        return $this->redirectToRoute('apm_transport_transporteur_index');
    }
}
