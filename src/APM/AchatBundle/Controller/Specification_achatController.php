<?php

namespace APM\AchatBundle\Controller;

use APM\AchatBundle\Entity\Specification_achat;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Specification_achat controller.
 *
 */
class Specification_achatController extends Controller
{
    /**
     * Lists all Specification_achat entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $specification_achats = $em->getRepository('APMAchatBundle:Specification_achat')->findAll();

        return $this->render('APMAchatBundle:specification_achat:index.html.twig', array(
            'specification_achats' => $specification_achats,
        ));
    }

    /**
     * Creates a new Specification_achat entity.
     *
     */
    public function newAction(Request $request)
    {
        $specification_achat = new Specification_achat();
        $form = $this->createForm('APM\AchatBundle\Form\Specification_achatType', $specification_achat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($specification_achat);
            $em->flush();

            return $this->redirectToRoute('apm_achat_specification_achat_show', array('id' => $specification_achat->getId()));
        }

        return $this->render('APMAchatBundle:specification_achat:new.html.twig', array(
            'specification_achat' => $specification_achat,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Specification_achat entity.
     *
     */
    public function showAction(Specification_achat $specification_achat)
    {
        $deleteForm = $this->createDeleteForm($specification_achat);

        return $this->render('APMAchatBundle:specification_achat:show.html.twig', array(
            'specification_achat' => $specification_achat,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Specification_achat entity.
     *
     * @param Specification_achat $specification_achat The Specification_achat entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Specification_achat $specification_achat)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_achat_specification_achat_delete', array('id' => $specification_achat->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Specification_achat entity.
     *
     */
    public function editAction(Request $request, Specification_achat $specification_achat)
    {
        $deleteForm = $this->createDeleteForm($specification_achat);
        $editForm = $this->createForm('APM\AchatBundle\Form\Specification_achatType', $specification_achat);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($specification_achat);
            $em->flush();

            return $this->redirectToRoute('apm_achat_specification_achat_show', array('id' => $specification_achat->getId()));
        }

        return $this->render('APMAchatBundle:specification_achat:edit.html.twig', array(
            'specification_achat' => $specification_achat,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Specification_achat entity.
     *
     */
    public function deleteAction(Request $request, Specification_achat $specification_achat)
    {
        $form = $this->createDeleteForm($specification_achat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($specification_achat);
            $em->flush();
        }

        return $this->redirectToRoute('apm_achat_specification_achat_index');
    }

    public function deleteFromListAction(Specification_achat $object)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($object);
        $em->flush();

        return $this->redirectToRoute('apm_achat_specification_achat_index');
    }
}
