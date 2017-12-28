<?php

namespace APM\VenteBundle\Controller;

use APM\TransportBundle\Entity\Livraison;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Boutique;

use APM\VenteBundle\Entity\Transaction;
use APM\VenteBundle\Entity\Transaction_produit;
use APM\VenteBundle\Factory\TradeFactory;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Put;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Transaction controller.
 * @RouteResource("transaction", pluralize=false)
 */
class TransactionController extends FOSRestController
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
    private $dateFrom_filter;
    private $dateTo_filter;

    /**
     * @ApiDoc(
     * resource=true,
     * description="Retrieve list of transactions.",
     * headers={
     *      { "name"="Authorization", "required"=true, "description"="Authorization token"},
     * },
     * filters={
     *      {"name"="dateFrom_filter", "dataType"="dateTime", "pattern"="19-12-2017|ASC"},
     *      {"name"="dateTo_filter", "dataType"="dateTime", "pattern"="19-12-2017|DESC"},
     *      {"name"="code_filter", "dataType"="string"},
     *      {"name"="etat_filter", "dataType"="integer"},
     *      {"name"="shipped_filter", "dataType"="boolean"},
     *      {"name"="nature_filter", "dataType"="string"},
     *      {"name"="boutiqueBeneficiaire_filter", "dataType"="string"},
     *      {"name"="nature_filter", "dataType"="string"},
     *      {"name"="boutique_filter", "dataType"="string"},
     *      {"name"="produit_filter", "dataType"="string"},
     *      {"name"="transactionProduit_filter", "dataType"="string"},
     *      {"name"="length", "dataType"="integer", "requirement"="\d+"},
     *      {"name"="start", "dataType"="integer", "requirement"="\d+"},
     *  },
     * parameters={
     *   {"name"="q", "dataType"="string", "required"=false, "description"="query: SENT | RECEIVED | DONE", "format"="?q=sent"}
     * },
     * output={
     *   "class"="APM\VenteBundle\Entity\Transaction",
     *   "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *    },
     *     "groups"={"owner_list"}
     * },
     *
     * statusCodes={
     *     "output" = "A single or a collection of transactions",
     *     200="Returned when successful",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "vente"}
     * )
     * @ParamConverter("livraison", options={"mapping": {"livraison_id":"id"}})
     * @param Request $request
     * @param Boutique $boutique
     * @param Livraison $livraison
     * @return JsonResponse
     *
     * @Get("/cget/transactions", name="s")
     * @Get("/cget/transactions/boutique/{id}", name="s_boutique", requirements={"id"="boutique_id"})
     * @Get("/cget/transactions/livraison/{livraison_id}", name="s_livraison", requirements={"livraison_id"="\d+"})
     */
    public function getAction(Request $request, Boutique $boutique = null, Livraison $livraison = null)
    {
        try {
            $this->listAndShowSecurity($boutique);
            /** @var Utilisateur_avm $user */
            $user = $this->getUser();
            $this->beneficiaire_filter = $request->query->has('beneficiaire_filter') ? $request->query->get('beneficiaire_filter') : "";
            $this->code_filter = $request->query->has('code_filter') ? $request->query->get('code_filter') : "";
            $this->nature_filter = $request->query->has('nature_filter') ? $request->query->get('nature_filter') : "";
            $this->etat_filter = $request->query->has('etat_filter') ? $request->query->get('etat_filter') : "";
            $this->montant_filter = $request->query->has('montant_filter') ? $request->query->get('montant_filter') : "";
            $this->shipped_filter = $request->query->has('shipped_filter') ? $request->query->get('shipped_filter') : "";
            $this->boutiqueBeneficiaire_filter = $request->query->has('boutiqueBeneficiaire_filter') ? $request->query->get('boutiqueBeneficiaire_filter') : "";
            $this->boutique_filter = $request->query->has('boutique_filter') ? $request->query->get('boutique_filter') : "";
            $this->transactionProduit_filter = $request->query->has('transactionProduit_filter') ? $request->query->get('transactionProduit_filter') : "";
            $this->produit_filter = $request->query->has('produit_filter') ? $request->query->get('produit_filter') : "";
            $this->dateFrom_filter = $request->query->has('dateFrom_filter') ? $request->query->get('dateFrom_filter') : "";
            $this->dateTo_filter = $request->query->has('dateTo_filter') ? $request->query->get('dateTo_filter') : "";
            $iDisplayLength = $request->query->has('length') ? $request->query->get('length') : -1;
            $iDisplayStart = $request->query->has('start') ? intval($request->query->get('start')) : 0;
            $json = array();
            $json['items'] = array();
            $q = $request->query->has('q') ? $request->query->get('q') : "all";
            if ($q === "sent" || $q === "all") {
                $transactionsEffectues = (null !== $boutique) ? $boutique->getTransactions() : $user->getTransactionsEffectues();
                $iTotalRecords = count($transactionsEffectues);
                if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
                $transactionsEffectues = $this->handleResults($transactionsEffectues, $iTotalRecords, $iDisplayStart, $iDisplayLength);
                $iFilteredRecords = count($transactionsEffectues);
                $data = $this->get('apm_core.data_serialized')->getFormalData($transactionsEffectues, array("owner_list"));
                $json['totalRecordsSent'] = $iTotalRecords;
                $json['filteredRecordsSent'] = $iFilteredRecords;
                $json['items'] = $data;
            }
            if ($q === "received" || $q === "all") {
                $transactionsRecues = (null !== $boutique) ? $boutique->getTransactionsRecues() : $user->getTransactionsRecues();
                $iTotalRecords = count($transactionsRecues);
                if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
                $transactionsRecues = $this->handleResults($transactionsRecues, $iTotalRecords, $iDisplayStart, $iDisplayLength);
                $iFilteredRecords = count($transactionsRecues);
                $data = $this->get('apm_core.data_serialized')->getFormalData($transactionsRecues, array("owner_list"));
                $json['totalRecordsReceived'] = $iTotalRecords;
                $json['filteredRecordsReceived'] = $iFilteredRecords;
                $json['items'] = $data;
            }
            if ($q === "done" || $q === "all") {
                $transactions = $livraison->getOperations();
                $iTotalRecords = count($transactions);
                if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
                $transactions = $this->handleResults($transactions, $iTotalRecords, $iDisplayStart, $iDisplayLength);
                $iFilteredRecords = count($transactions);
                $data = $this->get('apm_core.data_serialized')->getFormalData($transactions, array("owner_list"));
                $json['totalRecordsDone'] = $iTotalRecords;
                $json['filteredRecordsDone'] = $iFilteredRecords;
                $json['items'] = $data;
            }
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
                $beneficiaire = $transaction->getBeneficiaire();
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
        if ($this->dateFrom_filter != null) {
            $transactions = $transactions->filter(function ($e) {//start date
                /** @var Transaction $e */
                $dt1 = (new \DateTime($e->getDate()->format('d-m-Y')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateFrom_filter))->getTimestamp();
                return $dt1 - $dt2 >= 0 ? true : false; //start from the given date 'dateFrom_filter'
            });
        }
        if ($this->dateTo_filter != null) {
            $transactions = $transactions->filter(function ($e) {//end date
                /** @var Transaction $e */
                $dt = (new \DateTime($e->getDate()->format('d-m-Y')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateTo_filter))->getTimestamp();
                return $dt - $dt2 <= 0 ? true : false;// end from at the given date 'dateTo_filter'
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
     * @ApiDoc(
     * resource=true,
     * resourceDescription="Operations on Transaction.",
     * description="Create an object of type Transaction.",
     * statusCodes={
     *         201="Returned when successful",
     *         400="Returned when the data are not valid or an unknown error occurred",
     *         403="Returned when the user is not authorized to carry on the action",
     *         404="Returned when the entity is not found",
     * },
     * headers={
     *      { "name"="Authorization",  "required"=true, "description"="Authorization token"}
     * },
     *
     * input={
     *    "class"="APM\VenteBundle\Entity\Transaction",
     *     "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\ValidationParser"
     *      },
     *    "name" = "Transaction",
     * },
     *      views = {"default", "vente" }
     * )
     * @param Request $request
     * @param Boutique $boutique
     * @return View | JsonResponse
     *
     * @Post("/new/transaction")
     * @Post("/new/transaction/boutique/{id}", name="_boutique", requirements={"id"="boutique_id"})
     */
    public function newAction(Request $request, Boutique $boutique = null)
    {
        try {
            $this->createSecurity($boutique);
            /** @var Transaction $transaction */
            $transaction = TradeFactory::getTradeProvider('transaction');
            $form = $this->createForm('APM\VenteBundle\Form\TransactionType', $transaction);
            $form->submit($request->request->all());
            if (!$form->isValid()) {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $transaction->setBoutique($boutique);
            $transaction->setAuteur($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($transaction);
            $em->flush();
            return $this->routeRedirectView("api_vente_show_transaction", ['id' => $transaction->getId()], Response::HTTP_CREATED);
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
     * @ApiDoc(
     * resource=true,
     * description="Retrieve the details of an objet of type Transaction.",
     * headers={
     *      { "name"="Authorization", "required"=true, "description"="Authorization token"},
     * },
     * requirements = {
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="transaction id"}
     * },
     * output={
     *   "class"="APM\VenteBundle\Entity\Transaction",
     *   "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *    },
     *     "groups"={"owner_transaction_details", "owner_list"}
     * },
     * statusCodes={
     *     "output" = "A single Object",
     *     200="Returned when successful",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "vente"}
     * )
     * @param Transaction $transaction
     * @return JsonResponse
     *
     * @Get("/show/transaction/{id}")
     */
    public function showAction(Transaction $transaction)
    {
        $this->listAndShowSecurity($transaction->getBoutique(), $transaction);
        $data = $this->get('apm_core.data_serialized')->getFormalData($transaction, ["owner_transaction_details", "owner_list"]);
        return new JsonResponse($data, 200);
    }

    /**
     * @ApiDoc(
     * resource=true,
     * resourceDescription="Operations on Transaction",
     * description="Update an object of type Transaction.",
     * statusCodes={
     *         200="Returned when successful",
     *         400="Returned when the data are not valid or an unknown error occurred",
     *         403="Returned when the user is not authorized to carry on the action",
     *         404="Returned when the entity is not found",
     * },
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"}
     * },
     * requirements = {
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="transaction Id"}
     * },
     * input={
     *    "class"="APM\VenteBundle\Entity\Transaction",
     *     "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\ValidationParser"
     *      },
     *    "name" = "Transaction",
     * },
     *      views = {"default", "vente" }
     * )
     * @param Request $request
     * @param Transaction $transaction
     * @return View | JsonResponse
     *
     * @Put("/edit/transaction/{id}")
     */
    public function editAction(Request $request, Transaction $transaction)
    {
        try {
            $this->editAndDeleteSecurity($transaction);
            $form = $this->createForm('APM\VenteBundle\Form\TransactionType', $transaction);
            $form->submit($request->request->all(), false);
            if (!$form->isValid()) {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->routeRedirectView("api_vente_show_transaction", ['id' => $transaction->getId()], Response::HTTP_OK);
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
     * @ApiDoc(
     * resource=true,
     * description="Delete objet of type Transaction.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * requirements = {
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="transaction Id"}
     * },
     * parameters = {
     *      {"name"="exec", "required"=true, "dataType"="string", "requirement"="\D+", "description"="needed to check the origin of the request", "format"="exec=go"}
     * },
     * statusCodes={
     *     200="Returned when successful",
     *     400="Returned when the data are not valid or an unknown error occurred",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "vente"}
     * )
     * @param Request $request
     * @param Transaction $transaction
     * @return View | JsonResponse
     *
     * @Delete("/delete/transaction/{id}")
     */
    public function deleteAction(Request $request, Transaction $transaction)
    {
        try {
            $this->editAndDeleteSecurity($transaction);
            if (!$request->request->has('exec') || $request->request->get('exec') !== 'go') {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans('impossible de supprimer', [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            if ($boutique = $transaction->getBoutique()) {
                $route = "api_vente_get_transactions_boutique";
                $param = ["id" => $boutique->getId()];
            } else {
                $route = "api_vente_get_transactions";
                $param = [];
            }
            $em = $this->getDoctrine()->getManager();
            $em->remove($transaction);
            $em->flush();
            return $this->routeRedirectView($route, $param, Response::HTTP_OK);
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