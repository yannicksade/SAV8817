<?php

namespace APM\VenteBundle\Controller;

use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Boutique;
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
     * Liste les transactions d'un individu; effectuées et recues
     * @param Boutique $boutique
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Boutique $boutique = null)
    {
        $this->listAndShowSecurity($boutique);
        if ($boutique) {
            $transactionsEffectues = $boutique->getTransactions();
            $transactionsRecues = $boutique->getTransactionsRecues();
        } else {
            /** @var Utilisateur_avm $user */
            $user = $this->getUser();
            $transactionsEffectues = $user->getTransactionsEffectues();
            $transactionsRecues = $user->getTransactionsRecues();
        }

        return $this->render('APMVenteBundle:transaction:index.html.twig', array(
            'transactionsEffectues' => $transactionsEffectues,
            'transactionsRecues' => $transactionsRecues,
            'boutique' => $boutique,
        ));
    }

    /**
     * @param Boutique $boutique
     * @param Transaction $transaction
     */
    private function listAndShowSecurity($boutique, $transaction = null)
    {
        //-----------------------------------security-------------------------------------------

        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw $this->createAccessDeniedException();
        }
        $gerant = null;
        $proprietaire = null;
        $auteur = null;
        $beneficiaire = null;
        $vendeur = null;
        $user = $this->getUser();
        if ($boutique) {//autoriser un ayant droit à consulter ses transactions de droit
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
            if($beneficiaire)$beneficiaire = $transaction->getBeneficiaire();
        } else {
            if (null !== $transaction) {//s'il ne s'agit pas de la boutique, il peut s'agit de l'auteur ou du bénéficiaire qui veut avoir des informations
                $auteur = $transaction->getAuteur();
                if($beneficiaire)$beneficiaire = $transaction->getBeneficiaire();
            } else {
                $vendeur = $user; //autoriser l'utilisateur AVM si l'objet ne porte pas sur ressource dédiée: boutique
            }
        }
        if ($user !== $gerant && $user !== $proprietaire && $user !== $auteur && $user !== $beneficiaire && !$vendeur) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * Creates a new Transaction entity.
     * @param Request $request
     * @param Boutique $boutique
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request, Boutique $boutique = null)
    {
        $this->createSecurity($boutique);

        /** @var Transaction $transaction */
        $transaction = TradeFactory::getTradeProvider('transaction');
        $transaction->setBoutique($boutique);
        $form = $this->createForm('APM\VenteBundle\Form\TransactionType', $transaction);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->createSecurity($boutique);
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
     * @param Boutique $boutique
     */
    private function createSecurity($boutique)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        if ($boutique) {
            $user = $this->getUser();
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
            if ($user !== $gerant && $user !== $proprietaire) {
                throw $this->createAccessDeniedException();
            }
        }

        //----------------------------------------------------------------------------------------
    }

    /**
     * Finds and displays a Transaction entity.
     * @param Transaction $transaction
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Transaction $transaction)
    {
        $this->listAndShowSecurity($transaction->getBoutique(), $transaction);

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
        $this->editAndDeleteSecurity($transaction);
        $deleteForm = $this->createDeleteForm($transaction);
        $editForm = $this->createForm('APM\VenteBundle\Form\TransactionType', $transaction);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->editAndDeleteSecurity($transaction);
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
     * @param Transaction $transaction
     */
    private function editAndDeleteSecurity($transaction)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $gerant = null;
        $proprietaire = null;
        $auteur = null;
        $beneficiaire = null;
        $user = $this->getUser();
        $boutique = $transaction->getBoutique();
        if ($boutique) {
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
        } elseif ($transaction) {//s'il ne s'agit pas de la boutique, il peut s'agit de l'auteur ou du bénéficiaire qui veut modifier des informations
            $auteur = $transaction->getAuteur();
            $beneficiaire = $transaction->getBeneficiaire();
        }
        if ($user !== $gerant && $user !== $proprietaire && $user !== $auteur && $user !== $beneficiaire) {
            throw $this->createAccessDeniedException();
        }

        //----------------------------------------------------------------------------------------
    }

    /**
     * Deletes a Transaction entity.
     * @param Request $request
     * @param Transaction $transaction
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Transaction $transaction)
    {
        $this->editAndDeleteSecurity($transaction);
        $form = $this->createDeleteForm($transaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->editAndDeleteSecurity($transaction);
            $em = $this->getDoctrine()->getManager();
            $em->remove($transaction);
            $em->flush();
        }

        return $this->redirectToRoute('apm_vente_transaction_index');
    }

    public function deleteFromListAction(Transaction $transaction)
    {
        $this->editAndDeleteSecurity($transaction);
        $em = $this->getDoctrine()->getManager();
        $em->remove($transaction);
        $em->flush();

        return $this->redirectToRoute('apm_vente_transaction_index');
    }
}
