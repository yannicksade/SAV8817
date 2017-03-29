<?php

namespace APM\VenteBundle\Controller;

use APM\VenteBundle\Entity\Transaction;
use APM\VenteBundle\TradeAbstraction\Trade;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Transaction controller.
 *
 */
class TransactionController extends Controller
{

    /**
     * Lists all Transaction entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $transactions = $em->getRepository('APMVenteBundle:Transaction')->findAll();

        return $this->render('APMVenteBundle:transaction:index.html.twig', array(
            'transactions' => $transactions,
        ));
    }

    /**
     * Creates a new Transaction entity.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        /** @var Transaction $transaction */
        $transaction = Trade::getTradeProvider('transaction');
        $form = $this->createForm('APM\VenteBundle\Form\TransactionType', $transaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($transaction);
            $em->flush();

            return $this->redirectToRoute('apm_vente_transaction_show', array('id' => $transaction->getId()));
        }

        return $this->render('APMVenteBundle:transaction:new.html.twig', array(
            'transaction' => $transaction,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Transaction entity.
     * @param Transaction $transaction
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Transaction $transaction)
    {
        $deleteForm = $this->createDeleteForm($transaction);

        return $this->render('APMVenteBundle:transaction:show.html.twig', array(
            'transaction' => $transaction,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Transaction entity.
     *
     * @param Transaction $transaction The Transaction entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Transaction $transaction)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_vente_transaction_delete', array('id' => $transaction->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Transaction entity.
     * @param Request $request
     * @param Transaction $transaction
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Transaction $transaction)
    {
        $deleteForm = $this->createDeleteForm($transaction);
        $editForm = $this->createForm('APM\VenteBundle\Form\TransactionType', $transaction);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($transaction);
            $em->flush();

            return $this->redirectToRoute('apm_vente_transaction_show', array('id' => $transaction->getId()));
        }

        return $this->render('APMVenteBundle:transaction:edit.html.twig', array(
            'transaction' => $transaction,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Transaction entity.
     * @param Request $request
     * @param Transaction $transaction
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Transaction $transaction)
    {
        $form = $this->createDeleteForm($transaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($transaction);
            $em->flush();
        }

        return $this->redirectToRoute('apm_vente_transaction_index');
    }

    public function deleteFromListAction(Transaction $object)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($object);
        $em->flush();

        return $this->redirectToRoute('apm_vente_transaction_index');
    }
}
