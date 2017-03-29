<?php

namespace APM\TransportBundle\Controller;

use APM\TransportBundle\Entity\Livraison;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


/**
 * Livraison controller.
 *
 */
class LivraisonController extends Controller
{
    /**
     * Lists all Livraison entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $livraisons = $em->getRepository('APMTransportBundle:Livraison')->findAll();

        return $this->render('APMTransportBundle:livraison:index.html.twig', array(
            'livraisons' => $livraisons,
        ));
    }

    /**
     * Creates a new Livraison entity.
     *
     */
    public function newAction(Request $request)
    {
        $livraison = new Livraison();
        $form = $this->createForm('APM\TransportBundle\Form\LivraisonType', $livraison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($livraison);
            $em->flush();

            return $this->redirectToRoute('apm_transport_livraison_show', array('id' => $livraison->getId()));
        }

        return $this->render('APMTransportBundle:livraison:new.html.twig', array(
            'livraison' => $livraison,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Livraison entity.
     *
     */
    public function showAction(Livraison $livraison)
    {
        $deleteForm = $this->createDeleteForm($livraison);

        return $this->render('APMTransportBundle:livraison:show.html.twig', array(
            'livraison' => $livraison,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Livraison entity.
     *
     * @param Livraison $livraison The Livraison entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Livraison $livraison)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_transport_livraison_delete', array('id' => $livraison->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Livraison entity.
     *
     */
    public function editAction(Request $request, Livraison $livraison)
    {
        $deleteForm = $this->createDeleteForm($livraison);
        $editForm = $this->createForm('APM\TransportBundle\Form\LivraisonType', $livraison);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($livraison);
            $em->flush();

            return $this->redirectToRoute('apm_transport_livraison_show', array('id' => $livraison->getId()));
        }

        return $this->render('APMTransportBundle:livraison:edit.html.twig', array(
            'livraison' => $livraison,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Livraison entity.
     *
     */
    public function deleteAction(Request $request, Livraison $livraison)
    {
        $form = $this->createDeleteForm($livraison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($livraison);
            $em->flush();
        }

        return $this->redirectToRoute('apm_transport_livraison_index');
    }

    public function deleteFromListAction(Livraison $object)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($object);
        $em->flush();

        return $this->redirectToRoute('apm_transport_livraison_index');
    }
}
