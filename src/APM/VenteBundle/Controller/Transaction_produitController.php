<?php

namespace APM\VenteBundle\Controller;

use APM\VenteBundle\Entity\Offre;
use APM\VenteBundle\Entity\Transaction;
use APM\VenteBundle\Entity\Transaction_produit;
use APM\VenteBundle\Factory\TradeFactory;
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
     * @param Offre $offre
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Offre $offre)
    {
        $this->listAndShowSecurity($offre);
        $transaction_produits = $offre->getProduitTransactions();
        return $this->render('APMVenteBundle:transaction_produit:index.html.twig', array(
            'transaction_produits' => $transaction_produits,
        ));
    }


    public function newAction(Request $request)
    {
        $this->createSecurity();
        /** @var Transaction_produit $transaction_produit */
        $transaction_produit = TradeFactory::getTradeProvider('transaction_produit');
        /** @var Transaction $transaction */
        $transaction = TradeFactory::getTradeProvider('transaction');
        $form = $this->createForm('APM\VenteBundle\Form\Transaction_produitType', $transaction_produit);
        $form2 =$this->createForm('APM\VenteBundle\Form\TransactionType', $transaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form2->isSubmitted() && $form2->isValid()) {
            $this->createSecurity($form->getData()['offre']);
            $transaction->setAuteur($this->getUser());
            $transaction_produit->setTransaction($transaction);
            $em = $this->getDoctrine()->getManager();
            $em->persist($transaction_produit);
            $em->flush();

            return $this->redirectToRoute('apm_vente_transaction_produit_show', array('id' => $transaction_produit->getId()));
        }

        return $this->render('APMVenteBundle:transaction_produit:new.html.twig', array(
            'transaction_produit' => $transaction_produit,
            'form' => $form->createView(),
            'form2' => $form2->createView(),
        ));
    }

    /**
     * Finds and displays a Transaction_produit entity.
     * @param Transaction_produit $transaction_produit
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Transaction_produit $transaction_produit)
    {
        $this->listAndShowSecurity($transaction_produit->getProduit());
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
        $this->editAndDeleteSecurity();

        $deleteForm = $this->createDeleteForm($transaction_produit);
        $editForm = $this->createForm('APM\VenteBundle\Form\Transaction_produitType', $transaction_produit);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->editAndDeleteSecurity();
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
        $this->editAndDeleteSecurity();
        $form = $this->createDeleteForm($transaction_produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->editAndDeleteSecurity();
            $em = $this->getDoctrine()->getManager();
            $em->remove($transaction_produit);
            $em->flush();
        }

        return $this->redirectToRoute('apm_vente_transaction_produit_index');
    }

    public function deleteFromListAction(Transaction_produit $transaction_produit)
    {
        $this->editAndDeleteSecurity();
        $em = $this->getDoctrine()->getManager();
        $em->remove($transaction_produit);
        $em->flush();

        return $this->redirectToRoute('apm_vente_transaction_produit_index');
    }

    /**
     * @param Offre $offre
     */
    private function listAndShowSecurity($offre){
        //-----------------------------------security-------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');

            $user = $this->getUser();
            $vendeur = $offre->getVendeur();
            if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED') || $user !== $vendeur) {
                throw $this->createAccessDeniedException();
            }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @param Offre $offre
     */
    private function createSecurity($offre =null){
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')){
            throw $this->createAccessDeniedException();}
        $user = $this->getUser();
        $vendeur = $offre->getVendeur();
        if(null !==$offre && $user !== $vendeur) {
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
