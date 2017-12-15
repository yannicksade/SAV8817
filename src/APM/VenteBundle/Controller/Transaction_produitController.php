<?php

namespace APM\VenteBundle\Controller;

use APM\VenteBundle\Entity\Offre;
use APM\VenteBundle\Entity\Transaction;
use APM\VenteBundle\Entity\Transaction_produit;
use APM\VenteBundle\Factory\TradeFactory;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\Delete;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Transaction_produit controller.
 * @RouteResource("transaction-produit", pluralize=false)
 */
class Transaction_produitController extends FOSRestController
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
     * @param Request $request
     * @param Transaction $transaction
     * @return JsonResponse
     * Get("/cget/transaction-produits/transaction/{id}", name="s")
     */
    public function getAction(Request $request, Transaction $transaction)
    {
        try {
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
        } catch (AccessDeniedException $ads) {
            return new JsonResponse(
                [
                    "status" => 403,
                    "message" => $this->get('translator')->trans("Access denied", [], 'FOSUserBundle'),
                ]
                , Response::HTTP_FORBIDDEN
            );
        }
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
     * @return View | JsonResponse
     *
     * @Post("/new/transaction-produit")
     * @Post("/new/transaction-produit/transaction/{id}", name="_transaction")
     */
    public function newAction(Request $request, Transaction $transaction = null)
    {
        try {
            $this->createSecurity();
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
            $form->submit($request->request->all());
            if (!$form->isValid()) {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $this->createSecurity($transaction_produit->getProduit());
            $transaction_produit->setTransaction($transaction);
            $em->persist($transaction);
            $em->persist($transaction_produit);
            $em->flush();

            return $this->routeRedirectView("api_vente_show_transaction-produit", ['id' => $transaction_produit->getId()], Response::HTTP_CREATED);

        } catch (ConstraintViolationException $cve) {
            return new JsonResponse([
                "status" => 400,
                "message" => $this->get('translator')->trans("impossible d'enregistrer, vérifiez vos données", [], 'FOSUserBundle')
            ], Response::HTTP_BAD_REQUEST);
        } catch (AccessDeniedException $ads) {
            return new JsonResponse([
                    "status" => 403,
                    "message" => $this->get('translator')->trans("Access denied", [], 'FOSUserBundle'),
                ]
                , Response::HTTP_FORBIDDEN
            );
        }
    }

    /**
     * @param Offre $offre
     */
    private
    function createSecurity($offre = null)
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
     * @param Transaction_produit $transaction_produit
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Get("/show/transaction-produit/{id}")
     */
    public
    function showAction(Transaction_produit $transaction_produit)
    {
        $this->listAndShowSecurity($transaction_produit->getTransaction(), $transaction_produit->getProduit());
        $data = $this->get('apm_core.data_serialized')->getFormalData($transaction_produit, ["owner_transactionP_details", "owner_list"]);
        return new JsonResponse($data, 200);
    }

    /**
     * @param Request $request
     * @param Transaction_produit $transaction_produit
     * @return View | JsonResponse
     *
     * @Put("/edit/transaction-produit/{id}")
     */
    public
    function editAction(Request $request, Transaction_produit $transaction_produit)
    {
        try {
            $this->editAndDeleteSecurity($transaction_produit);
            $form = $this->createForm('APM\VenteBundle\Form\Transaction_produitType', $transaction_produit);
            $form->submit($request->request->all(), false);
            if (!$form->isValid()) {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->routeRedirectView("api_vente_show_transaction-produit", ['id' => $transaction_produit->getId()], Response::HTTP_OK);
        } catch (ConstraintViolationException $cve) {
            return new JsonResponse([
                "status" => 400,
                "message" => $this->get('translator')->trans("impossible d'enregistrer, vérifiez vos données", [], 'FOSUserBundle')
            ], Response::HTTP_BAD_REQUEST);
        } catch (AccessDeniedException $ads) {
            return new JsonResponse([
                    "status" => 403,
                    "message" => $this->get('translator')->trans("Access denied", [], 'FOSUserBundle'),
                ]
                , Response::HTTP_FORBIDDEN
            );
        }
    }

    /**
     * @param Transaction_produit $transaction_produit
     */
    private
    function editAndDeleteSecurity($transaction_produit)
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
     * @return View| JsonResponse
     *
     * @Delete("/delete/transaction-produit/{id}")
     */
    public
    function deleteAction(Request $request, Transaction_produit $transaction_produit)
    {
        try {
            $this->editAndDeleteSecurity($transaction_produit);
            if (!$request->request->has('exec') || $request->request->get('exec') !== 'go') {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans('impossible de supprimer', [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $transaction = $transaction_produit->getTransaction();
            $em = $this->getDoctrine()->getManager();
            $em->remove($transaction_produit);
            $em->flush();
            return $this->routeRedirectView("api_vente_new_transaction-produit_transaction", [$transaction->getId()], Response::HTTP_OK);
        } catch (ConstraintViolationException $cve) {
            return new JsonResponse(
                [
                    "status" => 400,
                    "message" => $this->get('translator')->trans("impossible de supprimer, vérifiez vos données", [], 'FOSUserBundle')
                ], Response::HTTP_FAILED_DEPENDENCY);
        } catch (AccessDeniedException $ads) {
            return new JsonResponse(
                [
                    "status" => 403,
                    "message" => $this->get('translator')->trans("Access denied", [], 'FOSUserBundle'),
                ]
                , Response::HTTP_FORBIDDEN
            );
        }

    }
}
