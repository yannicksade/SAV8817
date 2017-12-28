<?php

namespace APM\TransportBundle\Controller;

use APM\TransportBundle\Entity\Livraison;
use APM\TransportBundle\Entity\Profile_transporteur;
use APM\TransportBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Boutique;
use APM\VenteBundle\Entity\Transaction;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Patch;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


/**
 * Livraison controller.
 * @RouteResource("livraison", pluralize=false)
 */
class LivraisonController extends FOSRestController
{
    private $code_filter;
    private $livreur_boutique;
    private $description_filter;
    private $etat_filter;
    private $priorite_filter;
    private $valide_filter;
    private $dateEnregistrementTo_filter;
    private $dateEnregistrementFrom_filter;
    private $datePrevueFrom_filter;
    private $datePrevueTo_filter;

    /**
     * @ApiDoc(
     * resource=true,
     * description="Retrieve list of type Livraison.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * filters={
     *      {"name"="code_filter", "dataType"="string"},
     *      {"name"="datePrevueFrom_filter", "dataType"="dateTime", "pattern"="19-12-2017|ASC"},
     *      {"name"="datePrevueTo_filter", "dataType"="dateTime", "pattern"="19-12-2017|DESC"},
     *      {"name"="dateEnregistrementFrom_filter", "dataType"="dateTime", "pattern"="19-12-2017|ASC"},
     *      {"name"="dateEnregistrementTo_filter", "dataType"="dateTime", "pattern"="19-12-2017|DESC"},
     *      {"name"="description_filter", "dataType"="string"},
     *      {"name"="etat_filter", "dataType"="integer"},
     *      {"name"="livreur_boutique", "dataType"="string"},
     *      {"name"="priorite_filter", "dataType"="integer"},
     *      {"name"="valide_filter", "dataType"="boolean"},
     *      {"name"="length", "dataType"="integer", "requirement"="\d+"},
     *      {"name"="start", "dataType"="integer", "requirement"="\d+"},
     *  },
     *
     * output={
     *   "class"="APM\TransportBundle\Entity\Livraison",
     *   "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *    },
     *     "groups"={"owner_list"}
     * },
     * statusCodes={
     *     "output" = "A single or a collection of Livraison",
     *     200="Returned when successful",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "transport"}
     * )
     * @param Request $request
     * @param Boutique $boutique
     * @return JsonResponse
     *
     * @Get("/cget/livraisons", name="s")
     * @Get("/cget/livraisons/boutique/{id}", name="s_boutique", requirements={"id"="boutique_id"})
     */
    public function getAction(Request $request, Boutique $boutique = null)
    {
        try {
            $this->listeAndShowSecurity($boutique);
            if (null !== $boutique) {
                $livraisons = $boutique->getLivraisons();
            } else {
                /** @var Utilisateur_avm $user */
                $user = $this->getUser();
                $livraisons = $user->getLivraisons();
            }
            $json = array();
            $this->datePrevueFrom_filter = $request->query->has('datePrevueFrom_filter') ? $request->query->get('datePrevueFrom_filter') : "";
            $this->datePrevueTo_filter = $request->query->has('datePrevueTo_filter') ? $request->query->get('datePrevueTo_filter') : "";
            $this->dateEnregistrementFrom_filter = $request->query->has('dateEnregistrementFrom_filter') ? $request->query->get('dateEnregistrementFrom_filter') : "";
            $this->dateEnregistrementTo_filter = $request->query->has('dateEnregistrementTo_filter') ? $request->query->get('dateEnregistrementTo_filter') : "";
            $this->code_filter = $request->query->has('code_filter') ? $request->query->get('code_filter') : "";
            $this->livreur_boutique = $request->query->has('livreur_boutique') ? $request->query->get('livreur_boutique') : "";
            $this->description_filter = $request->query->has('description_filter') ? $request->query->get('description_filter') : "";
            $this->etat_filter = $request->query->has('etat_filter') ? $request->query->get('etat_filter') : "";
            $this->priorite_filter = $request->query->has('priorite_filter') ? $request->query->get('priorite_filter') : "";
            $this->valide_filter = $request->query->has('valide_filter') ? $request->query->get('valide_filter') : "";
            $iDisplayLength = $request->query->has('length') ? intval($request->query->get('length')) : -1;
            $iDisplayStart = $request->query->has('start') ? intval($request->query->get('start')) : 0;
            $iTotalRecords = count($livraisons);
            if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
            $livraisons = $this->handleResults($livraisons, $iTotalRecords, $iDisplayStart, $iDisplayLength);
            $iFilteredRecords = count($livraisons);
            $data = $this->get('apm_core.data_serialized')->getFormalData($livraisons, array("owner_list"));
            $json['totalRecords'] = $iTotalRecords;
            $json['filteredRecords'] = $iFilteredRecords;
            $json['items'] = $data;

            return new JsonResponse($json, 200);

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
     * @param Utilisateur_avm |null $beneficiaire
     */
    private function listeAndShowSecurity($boutique, $beneficiaire = null)
    {
        //-----------------------------------security-------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $user = $this->getUser();
        if ($boutique) {
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
            if ($user !== $gerant && $user !== $proprietaire) {
                throw $this->createAccessDeniedException();
            }
        } else {//donne la possibilité au bénéficiaire de voir les détails de la livraison
            if ($beneficiaire) {
                if ($user !== $beneficiaire) {
                    throw $this->createAccessDeniedException();
                }
            }
        }

        //------------------------------------------------------------------------------------------
    }

    /**
     * @param Collection $livraisons
     * @param $iTotalRecords
     * @param $iDisplayStart
     * @param $iDisplayLength
     * @return array
     */
    private function handleResults($livraisons, $iTotalRecords, $iDisplayStart, $iDisplayLength)
    {
        //filtering
        if ($livraisons === null) return array();

        if ($this->code_filter != null) {
            $livraisons = $livraisons->filter(function ($e) {//filtrage select
                /** @var Livraison $e */
                return $e->getCode() === $this->code_filter;
            });
        }
        if ($this->etat_filter != null) {
            $livraisons = $livraisons->filter(function ($e) {//filtrage select
                /** @var Livraison $e */
                return $e->getEtatLivraison() === $this->etat_filter;
            });
        }

        if ($this->valide_filter != null) {
            $livraisons = $livraisons->filter(function ($e) {//search for occurences in the text
                /** @var Livraison $e */
                return $e->getValide() === boolval($this->valide_filter);
            });
        }

        if ($this->datePrevueFrom_filter != null) {
            $livraisons = $livraisons->filter(function ($e) {//start date
                /** @var Livraison $e */
                $dt1 = (new \DateTime($e->getDateEtHeureLivraison()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->datePrevueFrom_filter))->getTimestamp();
                return $dt1 - $dt2 >= 0 ? true : false; //start from the given date 'dateFrom_filter'
            });
        }
        if ($this->datePrevueTo_filter != null) {
            $livraisons = $livraisons->filter(function ($e) {//end date
                /** @var Livraison $e */
                $dt = (new \DateTime($e->getDateEtHeureLivraison()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->datePrevueTo_filter))->getTimestamp();
                return $dt - $dt2 <= 0 ? true : false;// end from at the given date 'dateTo_filter'
            });
        }
        if ($this->dateEnregistrementFrom_filter != null) {
            $livraisons = $livraisons->filter(function ($e) {//start date
                /** @var Livraison $e */
                $dt1 = (new \DateTime($e->getDateEnregistrement()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateEnregistrementFrom_filter))->getTimestamp();
                return $dt1 - $dt2 >= 0 ? true : false; //start from the given date 'dateFrom_filter'
            });
        }
        if ($this->dateEnregistrementTo_filter != null) {
            $livraisons = $livraisons->filter(function ($e) {//end date
                /** @var Livraison $e */
                $dt = (new \DateTime($e->getDateEnregistrement()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateEnregistrementTo_filter))->getTimestamp();
                return $dt - $dt2 <= 0 ? true : false;// end from at the given date 'dateTo_filter'
            });
        }
        if ($this->description_filter != null) {
            $livraisons = $livraisons->filter(function ($e) {//search for occurences in the text
                /** @var Livraison $e */
                $subject = $e->getDescription();
                $pattern = $this->description_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }
        $livraisons = ($livraisons !== null) ? $livraisons->toArray() : [];
        //assortment: descending of date -- du plus recent au plus ancient
        usort(
            $livraisons, function ($e1, $e2) {
            /**
             * @var Livraison $e1
             * @var Livraison $e2
             */
            $dt1 = $e1->getDateEtHeureLivraison()->getTimestamp();
            $dt2 = $e2->getDateEtHeureLivraison()->getTimestamp();
            if ($dt1 === $dt2) $r = $e1->getPriorite() <= $e2->getPriorite() ? 1 : -1;
            else $r = $dt1 <= $dt2 ? 1 : -1;
            return $r;
        });

        if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
        //paging; slice and preserve keys' order
        $livraisons = array_slice($livraisons, $iDisplayStart, $iDisplayLength, true);

        return $livraisons;
    }

    /**
     * @ApiDoc(
     * resource=true,
     * resourceDescription="Operations on Livraison.",
     * description="Create an object of type livraison.",
     * statusCodes={
     *         201="Returned when successful",
     *         400="Returned when the data are not valid or an unknown error occurred",
     *         403="Returned when the user is not authorized to carry on the action",
     *         404="Returned when the entity is not found",
     * },
     * headers={
     *      { "name"="Authorization",  "required"=true, "description"="Authorization token"}
     * },
     * input={
     *    "class"="APM\TransportBundle\Entity\Livraison",
     *     "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\ValidationParser"
     *      },
     *    "name" = "Livraison",
     * },
     *      views = {"default", "transport" }
     * )
     * @ParamConverter("transaction", options={"mapping":{"transaction_id":"id"}})
     * Creates a new Livraison entity.
     * @param Request $request
     * @param Boutique $boutique
     * @param Transaction $transaction
     * @return View | JsonResponse
     *
     * @Post("/new/livraison")
     * @Post("/new/livraison/boutique/{id}", name="_boutique", requirements={"id"="boutique_id"})
     * @Post("/new/livraison/transaction/{transaction_id}", name="_transaction", requirements={"transaction_id"="\d+"})
     * @Post("/new/livraison/boutique/{id}/transaction/{transaction_id}", name="_boutique_transaction", requirements={"id"="boutique_id", "transaction_id"="\d+"})
     */
    public function newAction(Request $request, Boutique $boutique = null, Transaction $transaction = null)
    {
        try {
            $tr = [];
            if (null !== $transaction) $tr = new ArrayCollection([$transaction]);
            $this->createSecurity($boutique, $tr);
            /** @var Livraison $livraison */
            $livraison = TradeFactory::getTradeProvider("livraison");
            $form = $this->createForm('APM\TransportBundle\Form\LivraisonType', $livraison);
            if (null !== $transaction) $form->remove('operations');
            $form->submit($request->request->all());
            if (!$form->isValid()) {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $livraison->setUtilisateur($this->getUser());
            $livraison->setBoutique($boutique);
            if (null !== $transaction) {
                $transactions = $tr;
            } else {
                $transactions = $livraison->getOperations();
            }
            $this->createSecurity($boutique, $transactions);
            /** @var Transaction $transaction */
            foreach ($transactions as $transaction) {
                $transaction->setShipped(true);
                $transaction->setLivraison($livraison);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($livraison);
            $em->flush();

            return $this->routeRedirectView("api_transport_show_livraison", ['id' => $livraison->getId()], Response::HTTP_CREATED);

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
     * Verifie si la boutique appartient à son proprietaire ou le gerant
     * @param Collection $transactions
     */
    private function createSecurity($boutique, $transactions = null)
    {
        //--------security: verifie si l'utilisateur courant est le gerant de la boutique qui cree la livraison---------
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $user = $this->getUser();
        if (null !== $boutique) {
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
            if ($user !== $gerant && $user !== $proprietaire) {
                throw $this->createAccessDeniedException();
            }
        }
        if (null !== $transactions) {
            /** @var Transaction $operation */
            foreach ($transactions as $operation) {//ne pas livrer une opération plus d'une fois et
                if ($operation->isShipped() || $boutique !== $operation->getBoutique() || $user !== $operation->getAuteur() || null === $operation->getTransactionProduits()) {
                    throw $this->createAccessDeniedException();
                }
            }
        }
        //--------------------------------------------------------------------------------------------------------------
    }

    /**
     * @ApiDoc(
     * resource=true,
     * description="Retrieve the details of an objet of type livraison.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * requirements = {
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="livraison id"}
     * },
     * output={
     *   "class"="APM\TransportBundle\Entity\Livraison",
     *   "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *    },
     *     "groups"={"owner_livraison_details", "owner_list"}
     * },
     * statusCodes={
     *     "output" = "A single Object",
     *     200="Returned when successful",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "transport"}
     * )
     * @ParamConverter("transaction", options={"mapping":{"transaction_id":"id"}})
     * @param Livraison $livraison
     * @param Transaction $transaction
     * @return JsonResponse
     * @Get("/show/livraison/{id}", requirements={"id"="livraison_id"})
     * @Get("/show/livraison/{id}/transaction/{transaction_id}", name="_transaction", requirements={"id"="livraison_id", "transaction_id"="\d+"})
     */
    public function showAction(Livraison $livraison, Transaction $transaction = null)
    {
        if ($transaction) {
            $this->listeAndShowSecurity(null, $transaction->getBeneficiaire());
        } else {
            $this->listeAndShowSecurity($livraison->getBoutique());
        }
        $data = $this->get('apm_core.data_serialized')->getFormalData($livraison, ["owner_livraison_details", "owner_list"]);
        return new JsonResponse($data, 200);
    }

    /**
     * @ApiDoc(
     * resource=true,
     * resourceDescription="Operations on Livraison",
     * description="Update an object of type Livraison.",
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
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="livraison Id"}
     * },
     * input={
     *    "class"="APM\TransportBundle\Entity\Livraison",
     *     "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\ValidationParser"
     *      },
     *    "name" = "Transport",
     * },
     *
     * views = {"default", "transport" }
     * )
     * @param Request $request
     * @param Livraison $livraison
     * @return View | JsonResponse
     *
     * @Get("/edit/livraison/{id}")
     */
    public function editAction(Request $request, Livraison $livraison)
    {
        try {
            $this->editAndDeleteSecurity($livraison);
            $form = $this->createForm('APM\TransportBundle\Form\LivraisonType', $livraison);
            $form->submit($request->request->all(), false);
            if (!$form->isValid()) {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }

            $transactions = $livraison->getOperations();
            /** @var Transaction $transaction */
            foreach ($transactions as $transaction) {
                $transaction->setShipped(true);
                $transaction->setLivraison($livraison);
            }
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->routeRedirectView("api_transport_show_livraison", ['id' => $livraison->getId()], Response::HTTP_OK);
        } catch (ConstraintViolationException $cve) {
            return new JsonResponse(
                [
                    "status" => 400,
                    "message" => $this->get('translator')->trans("impossible d'enregistrer, vérifiez vos données", [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST
            );
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
     * @param Livraison $livraison
     */
    private function editAndDeleteSecurity($livraison)
    {//----- security : au cas ou il s'agirait d'une boutique vérifier le droit de l'utilisateur --------------------
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $user = $this->getUser();
        $boutique = $livraison->getBoutique();
        if (null !== $boutique) {
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
            if ($user !== $gerant && $user !== $proprietaire && $user !== $livraison->getUtilisateur()) {
                throw $this->createAccessDeniedException();
            }
        }
        //--------------------------------------------------------------------------------------------------------------

    }

    /**
     * @ApiDoc(
     * resource=true,
     * description="Delete objet of type livraison.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * requirements = {
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="livraison Id"}
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
     *     views={"default", "transport"}
     * )
     * @param Request $request
     * @param Livraison $livraison
     * @return View | JsonResponse
     *
     * @Delete("/delete/livraison/{id}")
     */
    public function deleteAction(Request $request, Livraison $livraison)
    {
        try {
            $this->editAndDeleteSecurity($livraison);
            if (!$request->request->has('exec') || $request->request->get('exec') !== 'go') {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans('impossible de supprimer', [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            if (null !== ($boutique = $livraison->getBoutique())) {
                $route = "api_transport_get_livraisons_boutique";
                $param = array("id" => $boutique->getId());
            } else {
                $route = "api_transport_get_livraisons";
                $param = array();
            }
            $em = $this->getDoctrine()->getManager();
            $em->remove($livraison);
            $em->flush();
            return $this->routeRedirectView($route, $param, Response::HTTP_OK);
        } catch (ConstraintViolationException $cve) {
            return new JsonResponse(
                [
                    "status" => 400,
                    "message" => $this->get('translator')->trans("impossible de supprimer, vérifiez vos données", [], 'FOSUserBundle')
                ], Response::HTTP_FAILED_DEPENDENCY
            );
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
