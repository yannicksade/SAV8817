<?php

namespace APM\UserBundle\Controller;

use APM\UserBundle\Entity\Individu_to_groupe;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Individu_to_groupe controller.
 *
 */
class Individu_to_groupeController extends Controller
{
    /**
     * Lists all Individu_to_groupe entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $individu_to_groupes = $em->getRepository('APMUserBundle:Individu_to_groupe')->findAll();

        return $this->render('APMUserBundle:individu_to_groupe:index.html.twig', array(
            'individu_to_groupes' => $individu_to_groupes,
        ));
    }

    /**
     * Creates a new Individu_to_groupe entity.
     *
     */
    public function newAction(Request $request)
    {
        $individu_to_groupe = new Individu_to_groupe();
        $form = $this->createForm('APM\UserBundle\Form\Individu_to_groupeType', $individu_to_groupe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($individu_to_groupe);
            $em->flush();

            return $this->redirectToRoute('apm_user_individu-to-groupe_show', array('id' => $individu_to_groupe->getId()));
        }

        return $this->render('APMUserBundle:individu_to_groupe:new.html.twig', array(
            'individu_to_groupe' => $individu_to_groupe,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Individu_to_groupe entity.
     *
     */
    public function showAction(Individu_to_groupe $individu_to_groupe)
    {
        $deleteForm = $this->createDeleteForm($individu_to_groupe);

        return $this->render('APMUserBundle:individu_to_groupe:show.html.twig', array(
            'individu_to_groupe' => $individu_to_groupe,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Individu_to_groupe entity.
     *
     * @param Individu_to_groupe $individu_to_groupe The Individu_to_groupe entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Individu_to_groupe $individu_to_groupe)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_user_individu-to-groupe_delete', array('id' => $individu_to_groupe->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Individu_to_groupe entity.
     *
     */
    public function editAction(Request $request, Individu_to_groupe $individu_to_groupe)
    {
        $deleteForm = $this->createDeleteForm($individu_to_groupe);
        $editForm = $this->createForm('APM\UserBundle\Form\Individu_to_groupeType', $individu_to_groupe);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($individu_to_groupe);
            $em->flush();

            return $this->redirectToRoute('apm_user_individu-to-groupe_show', array('id' => $individu_to_groupe->getId()));
        }

        return $this->render('APMUserBundle:individu_to_groupe:edit.html.twig', array(
            'individu_to_groupe' => $individu_to_groupe,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Individu_to_groupe entity.
     *
     */
    public function deleteAction(Request $request, Individu_to_groupe $individu_to_groupe)
    {
        $form = $this->createDeleteForm($individu_to_groupe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($individu_to_groupe);
            $em->flush();
        }

        return $this->redirectToRoute('apm_user_individu-to-groupe_index');
    }

    public function deleteFromListAction(Individu_to_groupe $object)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($object);
        $em->flush();

        return $this->redirectToRoute('apm_user_individu-to-groupe_index');
    }
}
