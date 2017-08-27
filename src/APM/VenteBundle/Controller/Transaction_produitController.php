<?php

namespace APM\VenteBundle\Controller;

use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Boutique;
use APM\VenteBundle\Entity\Offre;
use APM\VenteBundle\Entity\Transaction;
use APM\VenteBundle\Entity\Transaction_produit;
use APM\VenteBundle\Factory\TradeFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Transaction_produit controller.
 *
 */
class Transaction_produitController extends Controller
{

    /**
     *Liste les transactions produits d'une offre
     * @ParamConverter("offre", options={"mapping": {"offre_id":"id"}})
     * @param Boutique $boutique
     * @param Offre $offre
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Boutique $boutique = null, Offre $offre = null)
    {
        $this->listAndShowSecurity(null, $boutique, $offre);
        if ($offre) {
            $transactionsRecues = null;
            $transactionsEffectues [] = array(
                'transaction' => null,
                'transaction_produits' => $offre->getProduitTransactions()
            );
            $boutique = $offre->getBoutique();
        } else {
            if ($boutique) {
                $transactionEffectues = $boutique->getTransactions();
                $transactionRecues = $boutique->getTransactionsRecues();
            } else {
                /** @var Utilisateur_avm $user */
                $user = $this->getUser();
                $transactionEffectues = $user->getTransactionsEffectues();
                $transactionRecues = $user->getTransactionsRecues();
            }
            $transactionsEffectues = null;
            $transactionsRecues = null;
            /** @var Transaction $transactions */
            foreach ($transactionEffectues as $transactions) {
                $transactionsEffectues [] = array(
                    'transaction' => $transactions,
                    'transaction_produits' => $transactions->getTransactionProduits()
                );
            }
            /** @var Transaction $transactions */
            foreach ($transactionRecues as $transactions) {
                $transactionsRecues [] = array(
                    'transaction' => $transactions,
                    'transaction_produits' => $transactions->getTransactionProduits()
                );
            }
        }
        return $this->render('APMVenteBundle:transaction_produit:index.html.twig', array(
            'transactionsEffectues' => $transactionsEffectues,
            'transactionsRecues' => $transactionsRecues,
            'boutique' => $boutique,
            'offre' => $offre,
        ));
    }

    //liste les offres d'une transaction de produit

    /**
     * @param Transaction_produit $transaction_produit
     * @param Boutique $boutique
     * @param Offre $offre
     */
    private function listAndShowSecurity($transaction_produit, $boutique, $offre)
    {
        //-----------------------------------security-------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw $this->createAccessDeniedException();
        }

        $user = $this->getUser();
        $beneficiaire = null;
        $vendeur = null;
        $gerant = null;
        $proprietaire = null;
        if ($offre) {
            $vendeur = $offre->getVendeur();
            $boutique = $offre->getBoutique();
            $gerant = null;
            if ($boutique) {
                $gerant = $boutique->getGerant();
                $proprietaire = $boutique->getProprietaire();
                $vendeur = null;
            }
            if ($transaction_produit) {
                $beneficiaire = $transaction_produit->getTransaction()->getBeneficiaire();
            }
        } else {
            if ($boutique) {
                $gerant = $boutique->getGerant();
                $proprietaire = $boutique->getProprietaire();
                $vendeur = null;
            } else $vendeur = $user;
        }
        if ($user !== $beneficiaire && $user !== $gerant && $user !== $proprietaire && $user !== $vendeur) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    //créer une transaction produit liée à la création d'une transaction

    public function listeOffresAction(Transaction $transaction)
    {


        $offres = array();
        $categorie = null;
        $vendeur = null;
        $boutique = null;
        $count = 0;
        $transaction_produits = $transaction->getTransactionProduits();
        if(null !== $transaction_produits) {
            /** @var Transaction_produit $transaction_produit */
            foreach ($transaction_produits as $transaction_produit) {
                $count = array_push($offres, $transaction_produit->getProduit());
            }

            if (0 !== $count) {
                $anOffer = $offres[0];
                if ($anOffer) {
                    $vendeur = $anOffer->getVendeur();
                    $boutique = $anOffer->getBoutique();
                    }
            }

        }
        return $this->render('APMVenteBundle:offre:index.html.twig', array(
            'offres' => $offres,
            'boutique' => $boutique,
           'categorie' => $categorie,
             'vendeur' => $vendeur,
           'transaction' => $transaction,
        ));
    }

    /**
     * @param Request $request
     * @param Offre $offre
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request, Offre $offre = null)
    {
        $this->createSecurity($offre);
        /** @var Transaction_produit $transaction_produit */
        $transaction_produit = TradeFactory::getTradeProvider('transaction_produit');
        /** @var Transaction $transaction */
        $transaction = TradeFactory::getTradeProvider('transaction');
        $transaction_produit->setTransaction($transaction);
        if ($offre) $transaction_produit->setProduit($offre);
        $form = $this->createForm('APM\VenteBundle\Form\Transaction_produitType', $transaction_produit);
        if ($offre) $form->remove('produit');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$offre) {
                $offre = $form->get('produit')->getData();
            }
            $this->createSecurity($offre);
            $transaction->setBoutique($offre->getBoutique());
            $transaction->setAuteur($this->getUser());
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
     * @param Offre $offre
     */
    private function createSecurity($offre)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $user = $this->getUser();
        $vendeur = null;
        $gerant = null;
        $proprietaire = null;
        if ($offre) {
            $vendeur = $offre->getVendeur();
            $boutique = $offre->getBoutique();
            if ($boutique) {
                $gerant = $boutique->getGerant();
                $proprietaire = $boutique->getProprietaire();
                $vendeur = null;
            }

            if ($user !== $vendeur && $user !== $gerant && $user !== $proprietaire) {
                throw $this->createAccessDeniedException();
            }
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * Finds and displays a Transaction_produit entity.
     * @param Transaction_produit $transaction_produit
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Transaction_produit $transaction_produit)
    {
        $this->listAndShowSecurity($transaction_produit, null, $transaction_produit->getProduit());
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
     * Une Transaction produit ne peut être modifier ni supprimer car elle portant sur une offre AVM contrairement à une transaction
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
     * Une Transaction produit portant sur une offre ne peut être modifiée ni supprimée par l'utilisateur. Contrairement à une transaction
     */
    private function editAndDeleteSecurity()
    {
        //---------------------------------security-----------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
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
}
