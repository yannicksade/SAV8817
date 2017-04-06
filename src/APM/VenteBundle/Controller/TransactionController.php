<?php

namespace APM\VenteBundle\Controller;

use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Transaction;
use APM\VenteBundle\Factory\TradeFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Transaction controller.
 *
 */
class TransactionController extends Controller
{

    /**
     * Liste les transactions d'un individu
     *
     */
    public function indexAction()
    {
        $this->listAndShowSecurity();
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $transactions = $user->getTransactions();

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
        $this->createSecurity();

        /** @var Transaction $transaction */
        $transaction = TradeFactory::getTradeProvider('transaction');
        $form = $this->createForm('APM\VenteBundle\Form\TransactionType', $transaction);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->createSecurity();
            $transaction->setAuteur($this->getUser());
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
        $this->listAndShowSecurity();

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
        $this->editAndDeleteSecurity();
        $deleteForm = $this->createDeleteForm($transaction);
        $editForm = $this->createForm('APM\VenteBundle\Form\TransactionType', $transaction);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->editAndDeleteSecurity();
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
        $this->editAndDeleteSecurity();
        $form = $this->createDeleteForm($transaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->editAndDeleteSecurity();
            $em = $this->getDoctrine()->getManager();
            $em->remove($transaction);
            $em->flush();
        }

        return $this->redirectToRoute('apm_vente_transaction_index');
    }

    public function deleteFromListAction(Transaction $object)
    {
        $this->editAndDeleteSecurity();
        $em = $this->getDoctrine()->getManager();
        $em->remove($object);
        $em->flush();

        return $this->redirectToRoute('apm_vente_transaction_index');
    }

    private function listAndShowSecurity(){
        //-----------------------------------security-------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    private function createSecurity(){
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }


    private function editAndDeleteSecurity(){
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN', null, 'Unable to access this page!');

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }
}
