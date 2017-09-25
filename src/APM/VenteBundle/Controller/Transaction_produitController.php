<?php

namespace APM\VenteBundle\Controller;

use APM\VenteBundle\Entity\Offre;
use APM\VenteBundle\Entity\Transaction;
use APM\VenteBundle\Entity\Transaction_produit;
use APM\VenteBundle\Factory\TradeFactory;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Transaction_produit controller.
 *
 */
class Transaction_produitController extends Controller
{
    private $reference_filter;
    private $quantiteTo_filter;
    private $quantiteFrom_filter;
    private $designation_filter;
    private $codeTransaction_filter;
    private $designationProduit_filter;
    private $dateInsertionTo_filter;
    private $dateInsertionFrom_filter;

    /**
     * Liste les transactions produits d'une offre, d'une boutique ou d'un individu
     * @ParamConverter("offre", options={"mapping": {"offre_id":"id"}})
     * @param Request $request
     * @param Transaction $transaction
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, Transaction $transaction)
    {
        $this->listAndShowSecurity($transaction, null);
        $transaction_produits = $transaction->getTransactionProduits();
        if ($request->isXmlHttpRequest()) {
            $this->reference_filter = $request->request->has('reference_filter') ? $request->request->get('reference_filter') : "";
            $this->designation_filter = $request->request->has('designation_filter') ? $request->request->get('designation_filter') : "";
            $this->quantiteFrom_filter = $request->request->has('quantiteFrom_filter') ? $request->request->get('quantiteFrom_filter') : "";
            $this->quantiteTo_filter = $request->request->has('quantiteTo_filter') ? $request->request->get('quantiteTo_filter') : "";
            $this->codeTransaction_filter = $request->request->has('codeTransaction_filter') ? $request->request->get('codeTransaction_filter') : "";
            $this->dateInsertionFrom_filter = $request->request->has('dateInsertionFrom_filter') ? $request->request->get('dateInsertionFrom_filter') : "";
            $this->dateInsertionTo_filter = $request->request->has('dateInsertionTo_filter') ? $request->request->get('dateInsertionTo_filter') : "";
            $iDisplayLength = $request->request->has('length') ? $request->request->get('length') : -1;
            $iDisplayStart = $request->request->has('start') ? intval($request->request->get('start')) : 0;
            $json = array();
            $json['items'] = array();
            $iTotalRecords = count($transaction_produits);
            if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
            $transaction_produits = $this->handleResults($transaction_produits, $iTotalRecords, $iDisplayStart, $iDisplayLength);
            /** @var Transaction_produit $transaction_produit */
            foreach ($transaction_produits as $transaction_produit) {
                array_push($json['items'], array(
                    'value' => $transaction_produit->getId(),
                    'text' => $transaction_produit->getProduit()->getDesignation(),
                ));
            }
            return $this->json(json_encode($json), 200);
        }

        return $this->render('APMVenteBundle:transaction_produit:index.html.twig', array(
            'transactions' => $transaction_produits,
            'transaction' => $transaction
        ));
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

        if ($this->codeTransaction_filter != null) {
            $transactions = $transactions->filter(function ($e) {//filtrage select
                /** @var Transaction_produit $e */
                return $e->getTransaction()->getCode() === $this->codeTransaction_filter;
            });
        }

        if ($this->reference_filter != null) {
            $transactions = $transactions->filter(function ($e) {//filtrage select
                /** @var Transaction_produit $e */
                return $e->getReference() === $this->reference_filter;
            });
        }
        if ($this->quantiteFrom_filter != null) {
            $transactions = $transactions->filter(function ($e) {//start date
                /** @var Transaction_produit $e */
                return $e->getQuantite() >= $this->quantiteFrom_filter;
            });
        }
        if ($this->quantiteTo_filter != null) {
            $transactions = $transactions->filter(function ($e) {//start date
                /** @var Transaction_produit $e */
                return $e->getQuantite() <= $this->quantiteTo_filter;
            });
        }

        if ($this->dateInsertionFrom_filter != null) {
            $transactions = $transactions->filter(function ($e) {//start date
                /** @var Transaction_produit $e */
                $dt1 = (new \DateTime($e->getDateInsertion()->format('d-m-Y')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateInsertionFrom_filter))->getTimestamp();
                return $dt1 - $dt2 >= 0 ? true : false; //start from the given date 'dateFrom_filter'
            });
        }
        if ($this->dateInsertionTo_filter != null) {
            $transactions = $transactions->filter(function ($e) {//end date
                /** @var Transaction_produit $e */
                $dt = (new \DateTime($e->getDateInsertion()->format('d-m-Y')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateInsertionTo_filter))->getTimestamp();
                return $dt - $dt2 <= 0 ? true : false;// end from at the given date 'dateTo_filter'
            });
        }
        if ($this->designationProduit_filter != null) {
            $transactions = $transactions->filter(function ($e) {//search for occurences in the text
                /** @var Transaction_produit $e */
                $subject = $e->getProduit()->getDesignation();
                $pattern = $this->designationProduit_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }

        $transactions = ($transactions !== null) ? $transactions->toArray() : [];
        //assortment: descending of date -- du plus recent au plus ancient
        usort(
            $transactions, function ($e1, $e2) {
            /**
             * @var Transaction_produit $e1
             * @var Transaction_produit $e2
             */
            $dt1 = $e1->getDateInsertion()->getTimestamp();
            $dt2 = $e2->getDateInsertion()->getTimestamp();
            return $dt1 <= $dt2 ? 1 : -1;
        });
        if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
        //paging; slice and preserve keys' order
        $transactions = array_slice($transactions, $iDisplayStart, $iDisplayLength, true);

        return $transactions;
    }


    //liste les offres d'une transaction de produit

    /**
     * @param Transaction $transaction
     * @param Offre $offre
     * @internal param Transaction_produit $transaction_produit
     */
    private function listAndShowSecurity($transaction, $offre)
    {
        //-----------------------------------security-------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw $this->createAccessDeniedException();
        }

        $user = $this->getUser();
        $vendeur = null;
        $gerant = null;
        $proprietaire = null;
        if (null !== $offre) {
            $vendeur = $offre->getVendeur();
            $boutique = $offre->getBoutique();
            if (null !== $boutique) {
                $gerant = $boutique->getGerant();
                $proprietaire = $boutique->getProprietaire();
                $vendeur = null;
            }
        }
        $auteur = $transaction->getAuteur();
        if ($user !== $auteur && $user !== $gerant && $user !== $proprietaire && $user !== $vendeur && $user !== $transaction->getBeneficiaire()) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @ParamConverter("offre", options={"mapping": {"offre_id":"id"}})
     * @param Request $request
     * @param Transaction $transaction
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request, Transaction $transaction)
    {
        $this->createSecurity();
        /** @var Session $session */
        $session = $request->getSession();
        /** @var Transaction_produit $transaction_produit */
        $transaction_produit = TradeFactory::getTradeProvider('transaction_produit');
        $form = $this->createForm('APM\VenteBundle\Form\Transaction_produitType', $transaction_produit);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->createSecurity($transaction_produit->getProduit());
                $transaction_produit->setTransaction($transaction);
                $em = $this->getDoctrine()->getManager();
                if ($request->isXmlHttpRequest()) {
                    $json['item'] = array();
                    $data = $request->request->get('transaction_produit');
                    if (isset($data['quantite'])) $transaction_produit->setQuantite($data['quantite']);
                    if (isset($data['reference'])) $transaction_produit->setReference($data['reference']);
                    if (isset($data['produit']) && is_numeric($id = $data['produit'])) {
                        /** @var Offre $produit */
                        $produit = $em->getRepository('APMVenteBundle:Offre')->find($id);
                        $transaction_produit->setProduit($produit);
                    }
                    $em->persist($transaction_produit);
                    $em->flush();
                    $json["item"] = array(//prevenir le client
                        "action" => 0,
                    );
                    $session->getFlashBag()->add('success', "<strong> préparation de la transaction réf:" . $transaction_produit->getReference() . "</strong><br> Opération effectuée avec succès!");
                    return $this->json(json_encode($json), 200);
                }
                $em->persist($transaction_produit);
                $em->flush();
                return $this->redirectToRoute('apm_vente_transaction_produit_show', array('id' => $transaction_produit->getId()));
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
        return $this->render('APMVenteBundle:transaction_produit:new.html.twig', array(
            'transaction' => $transaction,
            'form' => $form->createView(),
        ));
    }

    /**
     * @param Offre $offre
     */
    private function createSecurity($offre = null)
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
        $this->listAndShowSecurity($transaction_produit->getTransaction(), $transaction_produit->getProduit());
        if ($request->isXmlHttpRequest()) {
            $json = array();
            $json['item'] = array(
                'id' => $transaction_produit->getId(),
                'code' => $transaction_produit->getTransaction()->getCode(),
                'reference' => $transaction_produit->getReference(),
                'quantite' => $transaction_produit->getQuantite(),
                'produit' => $transaction_produit->getProduit()->getDesignation(),
            );
            return $this->json(json_encode($json), 200);
        }
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
        $this->editAndDeleteSecurity($transaction_produit);
        if ($request->isXmlHttpRequest() && $request->getMethod() === "POST") {
            $json = array();
            $json['item'] = array();
            /** @var Session $session */
            $session = $request->getSession();
            $em = $this->getDoctrine()->getManager();
            $property = $request->request->get('name');
            $value = $request->request->get('value');
            switch ($property) {
                case 'reference':
                    $transaction_produit->setReference($value);
                    break;
                case 'quantite':
                    $transaction_produit->setQuantite($value);
                    break;
                default:
                    $session->getFlashBag()->add('info', "<strong> Aucune mis à jour effectuée</strong>");
                    return $this->json(json_encode(["item" => null]), 205);
            }
            $em->flush();
            $session->getFlashBag()->add('success', "Mis à jour propriété : <strong>" . $property . "</strong> réf. transaction :" . $transaction_produit->getReference() . "<br> Opération effectuée avec succès!");
            return $this->json(json_encode($json), 200);
        }

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
     * Une Transaction produit portant sur une offre ne peut être modifiée ni supprimée par l'utilisateur. Contrairement à une transaction
     * @param Transaction_produit $transaction_produit
     */
    private function editAndDeleteSecurity($transaction_produit)
    {
        //---------------------------------security-----------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') || $this->getUser() !== $transaction_produit->getTransaction()->getAuteur()) {
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
        $this->editAndDeleteSecurity($transaction_produit);
        $form = $this->createDeleteForm($transaction_produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($transaction_produit);
            $em->flush();
        }

        return $this->redirectToRoute('apm_vente_transaction_produit_index', ['id' => $transaction_produit->getTransaction()->getId()]);
    }

    public function deleteFromListAction(Transaction_produit $transaction_produit)
    {
        $this->editAndDeleteSecurity($transaction_produit);
        $em = $this->getDoctrine()->getManager();
        $em->remove($transaction_produit);
        $em->flush();
        if ($request->isXmlHttpRequest()) {
            $json = array();
            return $this->json($json, 200);
        }

        return $this->redirectToRoute('apm_vente_transaction_produit_index', ['id' => $transaction_produit->getTransaction()->getId()]);
    }
}
