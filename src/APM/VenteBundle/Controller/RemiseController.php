<?php

namespace APM\VenteBundle\Controller;

use APM\VenteBundle\Entity\Remise;
use APM\VenteBundle\TradeAbstraction\Trade;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Remise controller.
 *
 */
class RemiseController extends Controller
{

    /**
     * Lists all Remise entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $remises = $em->getRepository('APMVenteBundle:Remise')->findAll();

        return $this->render('APMVenteBundle:remise:index.html.twig', array(
            'remises' => $remises,
        ));
    }

    /**
     * Creates a new Remise entity.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        /** @var Remise $remise */
        $remise = Trade::getTradeProvider('remise');
        $form = $this->createForm('APM\VenteBundle\Form\RemiseType', $remise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($remise);
            $em->flush();

            return $this->redirectToRoute('apm_vente_remise_show', array('id' => $remise->getId()));
        }

        return $this->render('APMVenteBundle:remise:new.html.twig', array(
            'remise' => $remise,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Remise entity.
     * @param Remise $remise
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Remise $remise)
    {
        $deleteForm = $this->createDeleteForm($remise);

        return $this->render('APMVenteBundle:remise:show.html.twig', array(
            'remise' => $remise,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Remise entity.
     *
     * @param Remise $remise The Remise entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Remise $remise)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_vente_remise_delete', array('id' => $remise->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Remise entity.
     * @param Request $request
     * @param Remise $remise
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Remise $remise)
    {
        $deleteForm = $this->createDeleteForm($remise);
        $editForm = $this->createForm('APM\VenteBundle\Form\RemiseType', $remise);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($remise);
            $em->flush();

            return $this->redirectToRoute('apm_vente_remise_show', array('id' => $remise->getId()));
        }

        return $this->render('APMVenteBundle:remise:edit.html.twig', array(
            'remise' => $remise,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Remise entity.
     * @param Request $request
     * @param Remise $remise
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Remise $remise)
    {
        $form = $this->createDeleteForm($remise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($remise);
            $em->flush();
        }

        return $this->redirectToRoute('apm_vente_remise_index');
    }

    public function deleteFromListAction(Remise $object)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($object);
        $em->flush();

        return $this->redirectToRoute('apm_vente_remise_index');
    }
}
