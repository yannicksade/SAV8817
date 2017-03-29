<?php

namespace APM\MarketingDistribueBundle\Controller;

use APM\MarketingDistribueBundle\Entity\Commissionnement;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Commissionnement controller.
 *
 */
class CommissionnementController extends Controller
{
    /**
     * Lists all Commissionnement entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $commissionnements = $em->getRepository('APMMarketingDistribueBundle:Commissionnement')->findAll();

        return $this->render('APMMarketingDistribueBundle:commissionnement:index.html.twig', array(
            'commissionnements' => $commissionnements,
        ));
    }

    /**
     * Creates a new Commissionnement entity.
     *
     */
    public function newAction(Request $request)
    {
        $commissionnement = new Commissionnement();
        $form = $this->createForm('APM\MarketingDistribueBundle\Form\CommissionnementType', $commissionnement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($commissionnement);
            $em->flush();

            return $this->redirectToRoute('apm_marketing_commissionnement_show', array('id' => $commissionnement->getId()));
        }

        return $this->render('APMMarketingDistribueBundle:commissionnement:new.html.twig', array(
            'commissionnement' => $commissionnement,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Commissionnement entity.
     *
     */
    public function showAction(Commissionnement $commissionnement)
    {
        $deleteForm = $this->createDeleteForm($commissionnement);

        return $this->render('APMMarketingDistribueBundle:commissionnement:show.html.twig', array(
            'commissionnement' => $commissionnement,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Commissionnement entity.
     *
     * @param Commissionnement $commissionnement The Commissionnement entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Commissionnement $commissionnement)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_marketing_commissionnement_delete', array('id' => $commissionnement->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Commissionnement entity.
     *
     */
    public function editAction(Request $request, Commissionnement $commissionnement)
    {
        $deleteForm = $this->createDeleteForm($commissionnement);
        $editForm = $this->createForm('APM\MarketingDistribueBundle\Form\CommissionnementType', $commissionnement);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($commissionnement);
            $em->flush();

            return $this->redirectToRoute('apm_marketing_commissionnement_show', array('id' => $commissionnement->getId()));
        }

        return $this->render('APMMarketingDistribueBundle:commissionnement:edit.html.twig', array(
            'commissionnement' => $commissionnement,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Commissionnement entity.
     *
     */
    public function deleteAction(Request $request, Commissionnement $commissionnement)
    {
        $form = $this->createDeleteForm($commissionnement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($commissionnement);
            $em->flush();
        }

        return $this->redirectToRoute('apm_marketing_commissionnement_index');
    }

    public function deleteFromListAction(Commissionnement $object)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($object);
        $em->flush();

        return $this->redirectToRoute('apm_marketing_commissionnement_index');
    }
}
