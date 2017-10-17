<?php

namespace APM\VenteBundle\Controller;

use APM\TransportBundle\Entity\Livraison;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Boutique;
use APM\VenteBundle\Entity\Offre;
use APM\VenteBundle\Entity\Transaction;
use APM\VenteBundle\Entity\Transaction_produit;
use APM\VenteBundle\Factory\TradeFactory;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Transaction controller.
 *
 */
class TransactionController extends Controller
{
    private $beneficiaire_filter;
    private $montant_filter;
    private $etat_filter;
    private $code_filter;
    private $nature_filter;
    private $shipped_filter;
    private $boutiqueBeneficiaire_filter;
    private $boutique_filter;
    private $transactionProduit_filter;
    private $produit_filter;

    /** @ParamConverter("livraison", options={"mapping": {"livraison_id":"id"}})
     * Liste les transactions d'un individu; effectuées et recues
     * @param Request $request
     * @param Boutique $boutique
     * @param Livraison $livraison
     * @return \Symfony\Component\HttpFoundation\Response | JsonResponse
     */
    public function indexAction(Request $request, Boutique $boutique = null, Livraison $livraison = null)
    {
        $this->listAndShowSecurity($boutique);
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        if ($request->isXmlHttpRequest() && $request->getMethod() === "POST") {
            $q = $request->get('q');
            $this->beneficiaire_filter = $request->request->has('beneficiaire_filter') ? $request->request->get('beneficiaire_filter') : "";
            $this->code_filter = $request->request->has('code_filter') ? $request->request->get('code_filter') : "";
            $this->nature_filter = $request->request->has('nature_filter') ? $request->request->get('nature_filter') : "";
            $this->etat_filter = $request->request->has('etat_filter') ? $request->request->get('etat_filter') : "";
            $this->montant_filter = $request->request->has('montant_filter') ? $request->request->get('montant_filter') : "";

            $this->shipped_filter = $request->request->has('shipped_filter') ? $request->request->get('shipped_filter') : "";
            $this->boutiqueBeneficiaire_filter = $request->request->has('boutiqueBeneficiaire_filter') ? $request->request->get('boutiqueBeneficiaire_filter') : "";
            $this->boutique_filter = $request->request->has('boutique_filter') ? $request->request->get('boutique_filter') : "";
            $this->transactionProduit_filter = $request->request->has('transactionProduit_filter') ? $request->request->get('transactionProduit_filter') : "";
            $this->produit_filter = $request->request->has('produit_filter') ? $request->request->get('produit_filter') : "";

            $iDisplayLength = $request->request->has('length') ? $request->request->get('length') : -1;
            $iDisplayStart = $request->request->has('start') ? intval($request->request->get('start')) : 0;
            $json = array();
            $json['items'] = array();
            if ($q === "sent" || $q === "all") {
                $transactionsEffectues =  (null !== $boutique)? $boutique->getTransactions() : $user->getTransactionsEffectues();
                $iTotalRecords = count($transactionsEffectues);
                if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
                $transactionsEffectues = $this->handleResults($transactionsEffectues, $iTotalRecords, $iDisplayStart, $iDisplayLength);
                /** @var Transaction $transaction */
                foreach ($transactionsEffectues as $transaction) {
                    array_push($json, array(
                        'id' => $transaction->getId(),
                        'code' => $transaction->getCode(),
                        'nature' => $transaction->getNature(),
                    ));
                }
            }
            if ($q === "received" || $q === "all") {
                $transactionsRecues = (null !== $boutique)?$boutique->getTransactionsRecues(): $user->getTransactionsRecues();
                $iTotalRecords = count($transactionsRecues);
                if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
                $transactionsRecues = $this->handleResults($transactionsRecues, $iTotalRecords, $iDisplayStart, $iDisplayLength);
                foreach ($transactionsRecues as $transaction) {
                    array_push($json, array(
                        'id' => $transaction->getId(),
                        'code' => $transaction->getCode(),
                        'nature' => $transaction->getNature(),
                    ));
                }
            }
            if ($q === "done" || $q === "all") {
                $transactions = $livraison->getOperations();
                $iTotalRecords = count($transactions);
                if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
                $transactions = $this->handleResults($transactions, $iTotalRecords, $iDisplayStart, $iDisplayLength);
                foreach ($transactions as $transaction) {
                    array_push($json, array(
                        'value' => $transaction->getId(),
                        'text' => $transaction->getNature(),
                    ));
                }
            }

            return $this->json(json_encode($json), 200);
        }
        if (null !== $boutique) {
            $transactionsEffectues = $boutique->getTransactions();
            $transactionsRecues = $boutique->getTransactionsRecues();
        } elseif(null !== $livraison){
            $transactionsEffectues = $livraison->getOperations();
            $transactionsRecues = null;
        }else {
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
            if ($beneficiaire) $beneficiaire = $transaction->getBeneficiaire();
        } else {
            if (null !== $transaction) {//s'il ne s'agit pas de la boutique, il peut s'agit de l'auteur ou du bénéficiaire qui veut avoir des informations
                $auteur = $transaction->getAuteur();
                if ($beneficiaire) $beneficiaire = $transaction->getBeneficiaire();
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
     * @param Collection $transactions
     * @param $iTotalRecords
     * @param $iDisplayStart
     * @param $iDisplayLength
     * @return array
     */
    private function handleResults($transactions, $iTotalRecords, $iDisplayStart, $iDisplayLength)
    {
        //filtering
        if ($transactions === null) return array();

        if ($this->code_filter != null) {
            $transactions = $transactions->filter(function ($e) {//filtrage select
                /** @var Transaction $e */
                return $e->getCode() === $this->code_filter;
            });
        }
        if ($this->transactionProduit_filter != null) {
            $transactions = $transactions->filter(function ($e) {//filtrage select
                /** @var Transaction $e */
                return $e->getTransactionProduits() !== null;
            });
        }
        if ($this->etat_filter != null) {
            $transactions = $transactions->filter(function ($e) {//filtrage select
                /** @var Transaction $e */
                return $e->getStatut() === intval($this->etat_filter);
            });
        }
        if ($this->shipped_filter != null) {
            $transactions = $transactions->filter(function ($e) {//filtrage select
                /** @var Transaction $e */
                return $e->getShipped() === intval($this->shipped_filter);
            });
        }
        if ($this->montant_filter != null) {
            $transactions = $transactions->filter(function ($e) {//filtrage select
                /** @var Transaction $e */
                return $e->getMontant() === intval($this->montant_filter);
            });
        }

        if ($this->boutique_filter != null) {
            $transactions = $transactions->filter(function ($e) {//filter with the begining of the entering word
                /** @var Transaction $e */
                $str1 = $e->getBoutique()->getDesignation();
                $str2 = $this->boutique_filter;
                $len = strlen($str2);
                return strncasecmp($str1, $str2, $len) === 0 ? true : false;
            });
        }
        if ($this->boutiqueBeneficiaire_filter != null) {
            $transactions = $transactions->filter(function ($e) {//filter with the begining of the entering word
                /** @var Transaction $e */
                $str1 = $e->getBoutiqueBeneficiaire()->getDesignation();
                $str2 = $this->boutiqueBeneficiaire_filter;
                $len = strlen($str2);
                return strncasecmp($str1, $str2, $len) === 0 ? true : false;
            });
        }
        if ($this->produit_filter != null) {
            $transactions = $transactions->filter(function ($e) {//search for occurences in the text
                /** @var Transaction $e */
                $transactionsProduits = $e->getTransactionProduits();
                $name = array();
                $subject = '';
                /** @var Transaction_produit $transactionProduit */
                if (null !== $transactionsProduits) {
                    foreach ($transactionsProduits as $transactionProduit) {
                        $name[] = $transactionProduit->getProduit()->getDesignation();
                    }
                    $subject = join(' ', $name);
                }
                $pattern = $this->produit_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }
        if ($this->beneficiaire_filter != null) {
            $transactions = $transactions->filter(function ($e) {//search for occurences in the text
                /** @var Transaction $e */
                $subject = $e->getBeneficiaire();
                $pattern = $this->beneficiaire_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }
        if ($this->nature_filter != null) {
            $transactions = $transactions->filter(function ($e) {//search for occurences in the text
                /** @var Transaction $e */
                $subject = $e->getNature();
                $pattern = $this->nature_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }

        $transactions = ($transactions !== null) ? $transactions->toArray() : [];
        //assortment: descending of date -- du plus recent au plus ancient
        usort(
            $transactions, function ($e1, $e2) {
            /**
             * @var Transaction $e1
             * @var Transaction $e2
             */
            $dt1 = $e1->getDate()->getTimestamp();
            $dt2 = $e2->getDate()->getTimestamp();
            return $dt1 <= $dt2 ? 1 : -1;
        });
        if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
        //paging; slice and preserve keys' order
        $transactions = array_slice($transactions, $iDisplayStart, $iDisplayLength, true);

        return $transactions;
    }

    /**
     * Creates a new Transaction entity.
     * @param Request $request
     * @param Boutique $boutique
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response | JsonResponse
     */
    public function newAction(Request $request, Boutique $boutique = null)
    {
        $this->createSecurity($boutique);
        /** @var Session $session */
        $session = $request->getSession();
        /** @var Transaction $transaction */
        $transaction = TradeFactory::getTradeProvider('transaction');
        $form = $this->createForm('APM\VenteBundle\Form\TransactionType', $transaction);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $transaction->setBoutique($boutique);
                $transaction->setAuteur($this->getUser());
                $em = $this->getDoctrine()->getManager();
                $em->persist($transaction);
                $em->flush();
                if ($request->isXmlHttpRequest()) {
                    $json = array();
                    $json["item"] = array(//prevenir le client
                        "action" => 0,
                    );
                    $session->getFlashBag()->add('success', "<strong> transaction créée. réf:" . $transaction->getCode() . "</strong><br> Opération effectuée avec succès!");
                    return $this->json(json_encode($json), 200);
                }
                return $this->redirectToRoute('apm_vente_transaction_show', array('id' => $transaction->getId()));
            } catch (ConstraintViolationException $cve) {
                $session->getFlashBag()->add('danger', "<strong>Echec de l'enregistrement. </strong><br>L'enregistrement a échoué dû à une contrainte de données!");
                return $this->json(json_encode(["item" => null]));
            } catch (RuntimeException $rte) {
                $session->getFlashBag()->add('danger', "<strong>Echec de l'opération.</strong><br>L'enregistrement a échoué. bien vouloir réessayer plutard, svp!");
                return $this->json(json_encode(["item" => null]));
            } catch (AccessDeniedException $ads) {
                $session->getFlashBag()->add('danger', "<strong>Action interdite.</strong><br>Vous n'êtes pas autorisez à effectuer cette opération!");
                return $this->json(json_encode(["item" => null]));
            }
        }
        $session->set('previous_location', $request->getUri());
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
     * @param Request $request
     * @param Transaction $transaction
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, Transaction $transaction)
    {
        $this->listAndShowSecurity($transaction->getBoutique(), $transaction);
        if ($request->isXmlHttpRequest()) {
            $json = array();
            $json['item'] = array(
                'id' => $transaction->getId(),
                'code' => $transaction->getCode(),
                'nature' => $transaction->getNature(),
                'description' => $transaction->getDescription(),
                'etat' => $transaction->getStatut(),
                'montant' => $transaction->getMontant(),
                'beneficiaire' => $transaction->getBeneficiaire()->getId(),
                'auteur' => $transaction->getAuteur()->getId(),
                'destinataireNonAvm' => $transaction->getDestinataireNonAvm(),
                'date' => $transaction->getDate()->format('d-m-Y H:i')
            );
            return $this->json(json_encode($json), 200);
        }
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
        if ($request->isXmlHttpRequest() && $request->getMethod() === "POST") {
            $json = array();
            $json['item'] = array();
            /** @var Session $session */
            $session = $request->getSession();
            $em = $this->getDoctrine()->getManager();
            $property = $request->request->get('name');
            $value = $request->request->get('value');
            switch ($property) {
                case 'etat':
                    $transaction->setStatut($value);
                    $json["item"] = array(//pour actualiser la table
                        "action" => 1,
                    );
                    break;
                case 'destinataireNonAvm':
                    $transaction->setDestinataireNonAvm($value);
                    break;
                case 'montant':
                    $transaction->setMontant($value);
                    break;
                case 'nature' :
                    $transaction->setNature($value);
                    break;
                case 'beneficiaire':
                    /** @var Utilisateur_avm $beneficiaire */
                    $beneficiaire = $em->getRepository('APMUserBundle:Utilisateur_avm')->find($value);
                    $transaction->setBeneficiaire($beneficiaire);
                    break;
                default:
                    $session->getFlashBag()->add('info', "<strong> Aucune mis à jour effectuée</strong>");
                    return $this->json(json_encode(["item" => null]), 205);
            }
            $em->flush();
            $session->getFlashBag()->add('success', "Mis à jour propriété : <strong>" . $property . "</strong> réf. transaction :" . $transaction->getCode() . "<br> Opération effectuée avec succès!");
            return $this->json(json_encode($json), 200);
        }
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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse | JsonResponse
     */
    public function deleteAction(Request $request, Transaction $transaction)
    {
        $this->editAndDeleteSecurity($transaction);

        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest() && $request->getMethod() === "POST") {
            $em->remove($transaction);
            $em->flush();
            $json = array();
            return $this->json($json, 200);
        }

        $form = $this->createDeleteForm($transaction);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
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

    //créer une transaction produit liée à la création d'une transaction

    public function listeOffresAction(Request $request, Transaction $transaction)
    {
        if ($request->isXmlHttpRequest()) {
            $transaction_produits = $transaction->getTransactionProduits();
            $json = array();
            $json['items'] = array();
            if (null !== $transaction_produits) {
                /** @var Transaction_produit $transaction_produit */
                foreach ($transaction_produits as $transaction_produit) {
                    $offre = $transaction_produit->getProduit();
                    array_push($json['items'], array(
                        'value' => $offre->getId(),
                        'text' => $offre->getDesignation(),
                    ));
                }
            }
            return $this->json(json_encode($json), 200);
        } else {
            $offres = array();
            $categorie = null;
            $vendeur = null;
            $boutique = null;
            $transaction_produits = $transaction->getTransactionProduits();
            if (null !== $transaction_produits) {
                /** @var Transaction_produit $transaction_produit */
                foreach ($transaction_produits as $transaction_produit) {
                    $anOffer = $transaction_produit->getProduit();
                    $count = array_push($offres, $anOffer);
                    if (0 !== $count) {
                        $anOffer = $offres[0];
                        if ($anOffer) {
                            $vendeur = $anOffer->getVendeur();
                            $boutique = $anOffer->getBoutique();
                        }
                    }
                }
            }
        }
        return $this->render('APMVenteBundle:transaction_produit:index.html.twig', array(
            'offres' => $offres,
            'boutique' => $boutique,
            'categorie' => $categorie,
            'vendeur' => $vendeur,
            'transaction' => $transaction,
        ));
    }

}
