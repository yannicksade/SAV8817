<?php

namespace APM\VenteBundle\Controller;

use APM\VenteBundle\Entity\Transaction_produit;
use APM\VenteBundle\TradeAbstraction\Trade;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Transaction_produit controller.
 *
 */
class Transaction_produitController extends Controller
{

    /**
     * Lists all Transaction_produit entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $transaction_produits = $em->getRepository('APMVenteBundle:Transaction_produit')->findAll();

        return $this->render('APMVenteBundle:transaction_produit:index.html.twig', array(
            'transaction_produits' => $transaction_produits,
        ));
    }

    /**
     * Creates a new Transaction_produit entity.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        /** @var Transaction_produit $transaction_produit */
        $transaction_produit = Trade::getTradeProvider('transaction_produit');
        $form = $this->createForm('APM\VenteBundle\Form\Transaction_produitType', $transaction_produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($transaction_produit);
            $em->flush();

            return $this->redirectToRoute('apm_vente_transaction_produit_show', array('id' => $transaction_produit->getId()));
        }

        return $this->render('APMVenteBundle:transaction_produit:new.html.twig', array(
            'transaction_produit' => $transaction_produit,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Transaction_produit entity.
     * @param Transaction_produit $transaction_produit
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Transaction_produit $transaction_produit)
    {
        $deleteForm = $this->createDeleteForm($transaction_produit);

        return $this->render('APMVenteBundle:transaction_produit:show.html.twig', array(
            'transaction_produit' => $transaction_produit,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Transaction_produit entity.
     *
     * @param Transaction_produit $transaction_produit The Transaction_produit entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Transaction_produit $transaction_produit)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_vente_transaction_produit_delete', array('id' => $transaction_produit->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Transaction_produit entity.
     * @param Request $request
     * @param Transaction_produit $transaction_produit
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Transaction_produit $transaction_produit)
    {
        $deleteForm = $this->createDeleteForm($transaction_produit);
        $editForm = $this->createForm('APM\VenteBundle\Form\Transaction_produitType', $transaction_produit);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($transaction_produit);
            $em->flush();

            return $this->redirectToRoute('apm_vente_transaction_produit_show', array('id' => $transaction_produit->getId()));
        }

        return $this->render('APMVenteBundle:transaction_produit:edit.html.twig', array(
            'transaction_produit' => $transaction_produit,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Transaction_produit entity.
     * @param Request $request
     * @param Transaction_produit $transaction_produit
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Transaction_produit $transaction_produit)
    {
        $form = $this->createDeleteForm($transaction_produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($transaction_produit);
            $em->flush();
        }

        return $this->redirectToRoute('apm_vente_transaction_produit_index');
    }

    public function deleteFromListAction(Transaction_produit $object)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($object);
        $em->flush();

        return $this->redirectToRoute('apm_vente_transaction_produit_index');
    }
}
