<?php

namespace APM\UserBundle\Controller;

use APM\UserBundle\Entity\Groupe_relationnel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Groupe_relationnel controller.
 *
 */
class Groupe_relationnelController extends Controller
{
    /**
     * Lists all Groupe_relationnel entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $groupe_relationnels = $em->getRepository('APMUserBundle:Groupe_relationnel')->findAll();

        return $this->render('APMUserBundle:groupe_relationnel:index.html.twig', array(
            'groupe_relationnels' => $groupe_relationnels,
        ));
    }

    /**
     * Creates a new Groupe_relationnel entity.
     *
     */
    public function newAction(Request $request)
    {
        $groupe_relationnel = new Groupe_relationnel();
        $form = $this->createForm('APM\UserBundle\Form\Groupe_relationnelType', $groupe_relationnel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($groupe_relationnel);
            $em->flush();

            return $this->redirectToRoute('apm_user_groupe-relationnel_show', array('id' => $groupe_relationnel->getId()));
        }

        return $this->render('APMUserBundle:groupe_relationnel:new.html.twig', array(
            'groupe_relationnel' => $groupe_relationnel,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Groupe_relationnel entity.
     *
     */
    public function showAction(Groupe_relationnel $groupe_relationnel)
    {
        $deleteForm = $this->createDeleteForm($groupe_relationnel);

        return $this->render('APMUserBundle:groupe_relationnel:show.html.twig', array(
            'groupe_relationnel' => $groupe_relationnel,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Groupe_relationnel entity.
     *
     * @param Groupe_relationnel $groupe_relationnel The Groupe_relationnel entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Groupe_relationnel $groupe_relationnel)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_user_groupe-relationnel_delete', array('id' => $groupe_relationnel->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Groupe_relationnel entity.
     *
     */
    public function editAction(Request $request, Groupe_relationnel $groupe_relationnel)
    {
        $deleteForm = $this->createDeleteForm($groupe_relationnel);
        $editForm = $this->createForm('APM\UserBundle\Form\Groupe_relationnelType', $groupe_relationnel);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($groupe_relationnel);
            $em->flush();

            return $this->redirectToRoute('apm_user_groupe-relationnel_show', array('id' => $groupe_relationnel->getId()));
        }

        return $this->render('APMUserBundle:groupe_relationnel:edit.html.twig', array(
            'groupe_relationnel' => $groupe_relationnel,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Groupe_relationnel entity.
     *
     */
    public function deleteAction(Request $request, Groupe_relationnel $groupe_relationnel)
    {
        $form = $this->createDeleteForm($groupe_relationnel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($groupe_relationnel);
            $em->flush();
        }

        return $this->redirectToRoute('apm_user_groupe-relationnel_index');
    }

    public function deleteFromListAction(Groupe_relationnel $object)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($object);
        $em->flush();

        return $this->redirectToRoute('apm_user_groupe-relationnel_index');
    }
}
