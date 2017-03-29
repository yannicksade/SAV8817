<?php

namespace APM\TransportBundle\Controller;

use APM\TransportBundle\Entity\Livreur_boutique;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Livreur_boutique controller.
 *
 */
class Livreur_boutiqueController extends Controller
{
    /**
     * Lists all Livreur_boutique entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $livreur_boutiques = $em->getRepository('APMTransportBundle:Livreur_boutique')->findAll();

        return $this->render('APMTransportBundle:livreur_boutique:index.html.twig', array(
            'livreur_boutiques' => $livreur_boutiques,
        ));
    }

    /**
     * Creates a new Livreur_boutique entity.
     *
     */
    public function newAction(Request $request)
    {
        $livreur_boutique = new Livreur_boutique();
        $form = $this->createForm('APM\TransportBundle\Form\Livreur_boutiqueType', $livreur_boutique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($livreur_boutique);
            $em->flush();

            return $this->redirectToRoute('apm_transport_livreur_boutique_show', array('id' => $livreur_boutique->getId()));
        }

        return $this->render('APMTransportBundle:livreur_boutique:new.html.twig', array(
            'livreur_boutique' => $livreur_boutique,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Livreur_boutique entity.
     *
     */
    public function showAction(Livreur_boutique $livreur_boutique)
    {

        $deleteForm = $this->createDeleteForm($livreur_boutique);

        return $this->render('APMTransportBundle:livreur_boutique:show.html.twig', array(
            'livreur_boutique' => $livreur_boutique,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Livreur_boutique entity.
     *
     * @param Livreur_boutique $livreur_boutique The Livreur_boutique entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Livreur_boutique $livreur_boutique)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_transport_livreur_boutique_delete', array('id' => $livreur_boutique->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Livreur_boutique entity.
     *
     */
    public function editAction(Request $request, Livreur_boutique $livreur_boutique)
    {
        $deleteForm = $this->createDeleteForm($livreur_boutique);
        $editForm = $this->createForm('APM\TransportBundle\Form\Livreur_boutiqueType', $livreur_boutique);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($livreur_boutique);
            $em->flush();

            return $this->redirectToRoute('apm_transport_livreur_boutique_show', array('id' => $livreur_boutique->getId()));
        }

        return $this->render('APMTransportBundle:livreur_boutique:edit.html.twig', array(
            'livreur_boutique' => $livreur_boutique,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Livreur_boutique entity.
     *
     */
    public function deleteAction(Request $request, Livreur_boutique $livreur_boutique)
    {
        $form = $this->createDeleteForm($livreur_boutique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($livreur_boutique);
            $em->flush();
        }

        return $this->redirectToRoute('apm_transport_livreur_boutique_index');
    }

    public function deleteFromListAction(Livreur_boutique $object)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($object);
        $em->flush();

        return $this->redirectToRoute('apm_transport_livreur_boutique_index');
    }
}
