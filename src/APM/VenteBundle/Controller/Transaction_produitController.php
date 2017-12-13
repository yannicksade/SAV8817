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
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\Delete;

/**
 * Transaction_produit controller.
 * @RouteResource("transaction_produit", pluralize=false)
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
     * @return JsonResponse
     *
     * Get("/cget/transaction-produits/transaction/{id}", name="s")
     */
    public function getAction(Request $request, Transaction $transaction)
    {
        $this->listAndShowSecurity($transaction, null);
        $transaction_produits = $transaction->getTransactionProduits();

        $this->reference_filter = $request->query->has('reference_filter') ? $request->query->get('reference_filter') : "";
        $this->designation_filter = $request->query->has('designation_filter') ? $request->query->get('designation_filter') : "";
        $this->quantiteFrom_filter = $request->query->has('quantiteFrom_filter') ? $request->query->get('quantiteFrom_filter') : "";
        $this->quantiteTo_filter = $request->query->has('quantiteTo_filter') ? $request->query->get('quantiteTo_filter') : "";
        $this->codeTransaction_filter = $request->query->has('codeTransaction_filter') ? $request->query->get('codeTransaction_filter') : "";
        $this->dateInsertionFrom_filter = $request->query->has('dateInsertionFrom_filter') ? $request->query->get('dateInsertionFrom_filter') : "";
        $this->dateInsertionTo_filter = $request->query->has('dateInsertionTo_filter') ? $request->query->get('dateInsertionTo_filter') : "";
        $iDisplayLength = $request->query->has('length') ? $request->query->get('length') : -1;
        $iDisplayStart = $request->query->has('start') ? intval($request->query->get('start')) : 0;
        $json = array();
        $iTotalRecords = count($transaction_produits);
        if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
        $transaction_produits = $this->handleResults($transaction_produits, $iTotalRecords, $iDisplayStart, $iDisplayLength);
        $iFilteredRecords = count($transaction_produits);
        $data = $this->get('apm_core.data_serialized')->getFormalData($transaction_produits, array("owner_list"));
        $json['totalRecords'] = $iTotalRecords;
        $json['filteredRecords'] = $iFilteredRecords;
        $json['items'] = $data;
        return new JsonResponse($json, 200);
    }

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


    //liste les offres d'une transaction de produit

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

    /**
     * @param Request $request
     * @param Transaction $transaction
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response | JsonResponse
     *
     * @Post("/new/transaction-produit")
     * @Post("/new/transaction-produit/transaction/{id}", name="_transaction")
     */
    public function newAction(Request $request, Transaction $transaction = null)
    {
        $this->createSecurity();
        /** @var Session $session */
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        /** @var Transaction_produit $transaction_produit */
        $transaction_produit = TradeFactory::getTradeProvider('transaction_produit');
        $trans = null;
        if (null === $transaction) {
            /** @var Transaction $trans */
            $trans = TradeFactory::getTradeProvider('transaction');
            $trans->setAuteur($this->getUser());
            $transaction_produit->setTransaction($trans);
        }
        $form = $this->createForm('APM\VenteBundle\Form\Transaction_produitType', $transaction_produit);
        if (null !== $transaction) $form->remove('transaction'); else $transaction = $trans;
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->createSecurity($transaction_produit->getProduit());
                $transaction_produit->setTransaction($transaction);
                $em->persist($transaction);
                $em->persist($transaction_produit);
                $em->flush();
                if ($request->isXmlHttpRequest()) {
                    $json = array();
                    $json["item"] = array(//prevenir le client
                        "action" => 0,
                    );
                    $session->getFlashBag()->add('success', "<strong> préparation de la transaction réf:" . $transaction_produit->getReference() . "</strong><br> Opération effectuée avec succès!");
                    return $this->json(json_encode($json), 200);
                }
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
     *
     * @Get("/show/transaction-produit/{id}")
     */
    public function showAction(Transaction_produit $transaction_produit)
    {
        $this->listAndShowSecurity($transaction_produit->getTransaction(), $transaction_produit->getProduit());
        $data = $this->get('apm_core.data_serialized')->getFormalData($transaction_produit, ["owner_transactionP_details", "owner_list"]);
        return new JsonResponse($data, 200);
    }

    /**
     * Une Transaction produit ne peut être modifier ni supprimer car elle portant sur une offre AVM contrairement à une transaction
     * @param Request $request
     * @param Transaction_produit $transaction_produit
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Put("/edit/transaction-produit/{id}")
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
            $property = $request->query->get('name');
            $value = $request->query->get('value');
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
     * Deletes a Transaction_produit entity.
     * @param Request $request
     * @param Transaction_produit $transaction_produit
     * @return \Symfony\Component\HttpFoundation\RedirectResponse | JsonResponse
     *
     * @Delete("/delete/transaction-produit/{id}")
     */
    public function deleteAction(Request $request, Transaction_produit $transaction_produit)
    {
        $this->editAndDeleteSecurity($transaction_produit);
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {
            $json = array();
            $json['item'] = array();
            $em->remove($transaction_produit);
            $em->flush();
            return $this->json($json, 200);
        }

        $form = $this->createDeleteForm($transaction_produit);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->remove($transaction_produit);
            $em->flush();
        }

        return $this->redirectToRoute('apm_vente_transaction_produit_index', ['id' => $transaction_produit->getTransaction()->getId()]);
    }
}
