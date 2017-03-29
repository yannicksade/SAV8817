<?php

namespace APM\VenteBundle\Controller;

use APM\VenteBundle\Entity\Suggestion_produit;
use APM\VenteBundle\TradeAbstraction\Trade;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Suggestion_produit controller.
 *
 */
class Suggestion_produitController extends Controller
{

    /**
     * Lists all Suggestion_produit entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $suggestion_produits = $em->getRepository('APMVenteBundle:Suggestion_produit')->findAll();

        return $this->render('APMVenteBundle:suggestion_produit:index.html.twig', array(
            'suggestion_produits' => $suggestion_produits,
        ));
    }

    /**
     * Creates a new Suggestion_produit entity.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        /** @var Suggestion_produit $suggestion_produit */
        $suggestion_produit = Trade::getTradeProvider('suggestion');
        $form = $this->createForm('APM\VenteBundle\Form\Suggestion_produitType', $suggestion_produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($suggestion_produit);
            $em->flush();

            return $this->redirectToRoute('apm_vente_suggestion_offre_show', array('id' => $suggestion_produit->getId()));
        }

        return $this->render('APMVenteBundle:suggestion_produit:new.html.twig', array(
            'suggestion_produit' => $suggestion_produit,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Suggestion_produit entity.
     * @param Suggestion_produit $suggestion_produit
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Suggestion_produit $suggestion_produit)
    {
        $deleteForm = $this->createDeleteForm($suggestion_produit);

        return $this->render('APMVenteBundle:suggestion_produit:show.html.twig', array(
            'suggestion_produit' => $suggestion_produit,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Suggestion_produit entity.
     *
     * @param Suggestion_produit $suggestion_produit The Suggestion_produit entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Suggestion_produit $suggestion_produit)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_vente_suggestion_offre_delete', array('id' => $suggestion_produit->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Suggestion_produit entity.
     * @param Request $request
     * @param Suggestion_produit $suggestion_produit
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Suggestion_produit $suggestion_produit)
    {
        $deleteForm = $this->createDeleteForm($suggestion_produit);
        $editForm = $this->createForm('APM\VenteBundle\Form\Suggestion_produitType', $suggestion_produit);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($suggestion_produit);
            $em->flush();

            return $this->redirectToRoute('apm_vente_suggestion_offre_show', array('id' => $suggestion_produit->getId()));
        }

        return $this->render('APMVenteBundle:suggestion_produit:edit.html.twig', array(
            'suggestion_produit' => $suggestion_produit,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Suggestion_produit entity.
     * @param Request $request
     * @param Suggestion_produit $suggestion_produit
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Suggestion_produit $suggestion_produit)
    {
        $form = $this->createDeleteForm($suggestion_produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($suggestion_produit);
            $em->flush();
        }

        return $this->redirectToRoute('apm_vente_suggestion_offre_index');
    }

    public function deleteFromListAction(Suggestion_produit $object)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($object);
        $em->flush();

        return $this->redirectToRoute('apm_vente_suggestion_offre_index');
    }
}
