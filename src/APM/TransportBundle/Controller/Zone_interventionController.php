<?php

namespace APM\TransportBundle\Controller;

use APM\TransportBundle\Entity\Zone_intervention;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Zone_intervention controller.
 *
 */
class Zone_interventionController extends Controller
{
    /**
     * Lists all Zone_intervention entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $zone_interventions = $em->getRepository('APMTransportBundle:Zone_intervention')->findAll();

        return $this->render('APMTransportBundle:zone_intervention:index.html.twig', array(
            'zone_interventions' => $zone_interventions,
        ));
    }

    /**
     * Creates a new Zone_intervention entity.
     *
     */
    public function newAction(Request $request)
    {
        $zone_intervention = new Zone_intervention();
        $form = $this->createForm('APM\TransportBundle\Form\Zone_interventionType', $zone_intervention);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($zone_intervention);
            $em->flush();

            return $this->redirectToRoute('apm_zone_intervention_show', array('id' => $zone_intervention->getId()));
        }

        return $this->render('APMTransportBundle:zone_intervention:new.html.twig', array(
            'zone_intervention' => $zone_intervention,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Zone_intervention entity.
     *
     */
    public function showAction(Zone_intervention $zone_intervention)
    {
        $deleteForm = $this->createDeleteForm($zone_intervention);

        return $this->render('APMTransportBundle:zone_intervention:show.html.twig', array(
            'zone_intervention' => $zone_intervention,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Zone_intervention entity.
     *
     * @param Zone_intervention $zone_intervention The Zone_intervention entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Zone_intervention $zone_intervention)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_zone_intervention_delete', array('id' => $zone_intervention->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Zone_intervention entity.
     *
     */
    public function editAction(Request $request, Zone_intervention $zone_intervention)
    {
        $deleteForm = $this->createDeleteForm($zone_intervention);
        $editForm = $this->createForm('APM\TransportBundle\Form\Zone_interventionType', $zone_intervention);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($zone_intervention);
            $em->flush();

            return $this->redirectToRoute('apm_zone_intervention_show', array('id' => $zone_intervention->getId()));
        }

        return $this->render('APMTransportBundle:zone_intervention:edit.html.twig', array(
            'zone_intervention' => $zone_intervention,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Zone_intervention entity.
     *
     */
    public function deleteAction(Request $request, Zone_intervention $zone_intervention)
    {
        $form = $this->createDeleteForm($zone_intervention);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($zone_intervention);
            $em->flush();
        }

        return $this->redirectToRoute('apm_zone_intervention_index');
    }

    public function deleteFromListAction(Zone_intervention $object)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($object);
        $em->flush();

        return $this->redirectToRoute('apm_zone_intervention_index');
    }
}
