<?php

namespace APM\MarketingDistribueBundle\Controller;

use APM\MarketingDistribueBundle\Entity\Quota;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Quota controller.
 *
 */
class QuotaController extends Controller
{
    /**
     * Lists all Quota entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $quotas = $em->getRepository('APMMarketingDistribueBundle:Quota')->findAll();

        return $this->render('APMMarketingDistribueBundle:quota:index.html.twig', array(
            'quotas' => $quotas,
        ));
    }

    /**
     * Creates a new Quota entity.
     *
     */
    public function newAction(Request $request)
    {
        $quotum = new Quota();
        $form = $this->createForm('APM\MarketingDistribueBundle\Form\QuotaType', $quotum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($quotum);
            $em->flush();

            return $this->redirectToRoute('apm_marketing_quota_show', array('id' => $quotum->getId()));
        }

        return $this->render('APMMarketingDistribueBundle:quota:new.html.twig', array(
            'quotum' => $quotum,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Quota entity.
     *
     */
    public function showAction(Quota $quotum)
    {
        $deleteForm = $this->createDeleteForm($quotum);

        return $this->render('APMMarketingDistribueBundle:quota:show.html.twig', array(
            'quotum' => $quotum,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Quota entity.
     *
     * @param Quota $quotum The Quota entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Quota $quotum)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_marketing_quota_delete', array('id' => $quotum->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Quota entity.
     *
     */
    public function editAction(Request $request, Quota $quotum)
    {
        $deleteForm = $this->createDeleteForm($quotum);
        $editForm = $this->createForm('APM\MarketingDistribueBundle\Form\QuotaType', $quotum);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($quotum);
            $em->flush();

            return $this->redirectToRoute('apm_marketing_quota_show', array('id' => $quotum->getId()));
        }

        return $this->render('APMMarketingDistribueBundle:quota:edit.html.twig', array(
            'quotum' => $quotum,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Quota entity.
     *
     */
    public function deleteAction(Request $request, Quota $quotum)
    {
        $form = $this->createDeleteForm($quotum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($quotum);
            $em->flush();
        }

        return $this->redirectToRoute('apm_marketing_quota_index');
    }

    public function deleteFromListAction(Quota $object)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($object);
        $em->flush();

        return $this->redirectToRoute('apm_marketing_quota_index');
    }
}
