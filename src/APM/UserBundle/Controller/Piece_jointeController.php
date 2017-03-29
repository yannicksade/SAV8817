<?php

namespace APM\UserBundle\Controller;

use APM\UserBundle\Entity\Piece_jointe;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Piece_jointe controller.
 *
 */
class Piece_jointeController extends Controller
{
    /**
     * Lists all Piece_jointe entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $piece_jointes = $em->getRepository('APMUserBundle:Piece_jointe')->findAll();

        return $this->render('APMUserBundle:piece_jointe:index.html.twig', array(
            'piece_jointes' => $piece_jointes,
        ));
    }

    /**
     * Creates a new Piece_jointe entity.
     *
     */
    public function newAction(Request $request)
    {
        $piece_jointe = new Piece_jointe();
        $form = $this->createForm('APM\UserBundle\Form\Piece_jointeType', $piece_jointe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($piece_jointe);
            $em->flush();

            return $this->redirectToRoute('apm_user_piece-jointe_show', array('id' => $piece_jointe->getId()));
        }

        return $this->render('APMUserBundle:piece_jointe:new.html.twig', array(
            'piece_jointe' => $piece_jointe,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Piece_jointe entity.
     *
     */
    public function showAction(Piece_jointe $piece_jointe)
    {
        $deleteForm = $this->createDeleteForm($piece_jointe);

        return $this->render('APMUserBundle:piece_jointe:show.html.twig', array(
            'piece_jointe' => $piece_jointe,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Piece_jointe entity.
     *
     * @param Piece_jointe $piece_jointe The Piece_jointe entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Piece_jointe $piece_jointe)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_user_piece-jointe_delete', array('id' => $piece_jointe->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Piece_jointe entity.
     *
     */
    public function editAction(Request $request, Piece_jointe $piece_jointe)
    {
        $deleteForm = $this->createDeleteForm($piece_jointe);
        $editForm = $this->createForm('APM\UserBundle\Form\Piece_jointeType', $piece_jointe);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($piece_jointe);
            $em->flush();

            return $this->redirectToRoute('apm_user_piece-jointe_show', array('id' => $piece_jointe->getId()));
        }

        return $this->render('APMUserBundle:piece_jointe:edit.html.twig', array(
            'piece_jointe' => $piece_jointe,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Piece_jointe entity.
     *
     */
    public function deleteAction(Request $request, Piece_jointe $piece_jointe)
    {
        $form = $this->createDeleteForm($piece_jointe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($piece_jointe);
            $em->flush();
        }

        return $this->redirectToRoute('apm_user_piece-jointe_index');
    }

    public function deleteFromListAction(Piece_jointe $object)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($object);
        $em->flush();

        return $this->redirectToRoute('apm_user_piece-jointe_index');
    }
}
