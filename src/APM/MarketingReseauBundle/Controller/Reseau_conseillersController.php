<?php

namespace APM\MarketingReseauBundle\Controller;

use APM\MarketingReseauBundle\Entity\Reseau_conseillers;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Reseau_conseillers controller.
 *
 */
class Reseau_conseillersController extends Controller
{
    /**
     * Lists all Reseau_conseillers entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $reseau_conseillers = $em->getRepository('APMMarketingReseauBundle:Reseau_conseillers')->findAll();

        return $this->render('APMMarketingReseauBundle:reseau_conseillers:index.html.twig', array(
            'reseau_conseillers' => $reseau_conseillers,
        ));
    }

    /**
     * Creates a new Reseau_conseillers entity.
     *
     */
    public function newAction(Request $request)
    {
        $reseau_conseiller = new Reseau_conseillers();
        $form = $this->createForm('APM\MarketingReseauBundle\Form\Reseau_conseillersType', $reseau_conseiller);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($reseau_conseiller);
            $em->flush();

            return $this->redirectToRoute('apm_marketing_reseau_conseillers_show', array('id' => $reseau_conseiller->getId()));
        }

        return $this->render('APMMarketingReseauBundle:reseau_conseillers:new.html.twig', array(
            'reseau_conseiller' => $reseau_conseiller,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Reseau_conseillers entity.
     *
     */
    public function showAction(Reseau_conseillers $reseau_conseiller)
    {
        $deleteForm = $this->createDeleteForm($reseau_conseiller);

        return $this->render('APMMarketingReseauBundle:reseau_conseillers:show.html.twig', array(
            'reseau_conseiller' => $reseau_conseiller,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Reseau_conseillers entity.
     *
     * @param Reseau_conseillers $reseau_conseiller The Reseau_conseillers entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Reseau_conseillers $reseau_conseiller)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_marketing_reseau_conseillers_delete', array('id' => $reseau_conseiller->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Reseau_conseillers entity.
     *
     */
    public function editAction(Request $request, Reseau_conseillers $reseau_conseiller)
    {
        $deleteForm = $this->createDeleteForm($reseau_conseiller);
        $editForm = $this->createForm('APM\MarketingReseauBundle\Form\Reseau_conseillersType', $reseau_conseiller);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($reseau_conseiller);
            $em->flush();

            return $this->redirectToRoute('apm_marketing_reseau_conseillers_show', array('id' => $reseau_conseiller->getId()));
        }

        return $this->render('APMMarketingReseauBundle:reseau_conseillers:edit.html.twig', array(
            'reseau_conseiller' => $reseau_conseiller,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Reseau_conseillers entity.
     *
     */
    public function deleteAction(Request $request, Reseau_conseillers $reseau_conseiller)
    {
        $form = $this->createDeleteForm($reseau_conseiller);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($reseau_conseiller);
            $em->flush();
        }

        return $this->redirectToRoute('apm_marketing_reseau_conseillers_index');
    }

    public function deleteFromListAction(Reseau_conseillers $object)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($object);
        $em->flush();

        return $this->redirectToRoute('apm_marketing_reseau_conseillers_index');
    }
}
