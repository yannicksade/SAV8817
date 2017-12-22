<?php

namespace APM\UserBundle\Controller;

use APM\UserBundle\Entity\Communication;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\UserBundle\Factory\TradeFactory;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Put;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Communication controller.
 * @RouteResource("communication", pluralize=false)
 */
class CommunicationController extends FOSRestController
{
    private $code_filter;
    private $contenu_filter;
    private $etat_filter;
    private $reference_filter;
    private $date_filter;
    private $type_filter;
    private $valide_filter;
    private $emetteur_filter;
    private $recepteur_filter;
    private $dateDeVigueurFrom_filter;
    private $dateDeVigueurTo_filter;
    private $dateFinFrom_filter;
    private $dateFinTo_filter;

    /**
     * @ApiDoc(
     * resource=true,
     * description="Retrieve list of communications sent and received.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * filters={
     *      {"name"="code_filter", "dataType"="string"},
     *      {"name"="contenu_filter", "dataType"="string"},
     *      {"name"="dateDeVigueurFrom_filter", "dataType"="dateTime", "pattern"="19-12-2017|ASC"},
     *      {"name"="dateDeVigueurTo_filter", "dataType"="dateTime", "pattern"="19-12-2017|DESC"},
     *      {"name"="dateFinFrom_filter", "dataType"="dateTime", "pattern"="19-12-2017|ASC"},
     *      {"name"="dateFinTo_filter", "dataType"="dateTime", "pattern"="19-12-2017|DESC"},
     *      {"name"="date_filter", "dataType"="dateTime", "pattern"="19-12-2017"},
     *      {"name"="etat_filter", "dataType"="integer"},
     *      {"name"="type_filter", "dataType"="integer"},
     *      {"name"="description_filter", "dataType"="string"},
     *      {"name"="reference_filter", "dataType"="string"},
     *      {"name"="valide_filter", "dataType"="boolean"},
     *      {"name"="emetteur_filter", "dataType"="string"},
     *      {"name"="recepteur_filter", "dataType"="string"},
     *      {"name"="length_filter", "dataType"="integer", "requirement"="\d+"},
     *      {"name"="start_filter", "dataType"="integer", "requirement"="\d+"},
     *  },
     * requirements={
     *   {"name"="q", "required"=false, "dataType"="string", "requirement"="\D+", "description"="query request ==sender or receiver== e.g ?q=sender"}
     * },
     * output={
     *   "class"="APM\UserBundle\Entity\Communication",
     *   "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *    },
     *     "groups"={"owner_list"}
     * },
     * statusCodes={
     *     "output" = "A single or a collection of communications",
     *     200="Returned when successful",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "user"}
     * )
     * @param Request $request
     * @return JsonResponse
     *
     * @Get("/cget/communications", name="s")
     */
    public function getAction(Request $request)
    {
        try {
            $this->listAndShowSecurity();
            /** @var Utilisateur_avm $user */
            $user = $this->getUser();
            $this->dateDeVigueurFrom_filter = $request->query->has('dateDeVigueurFrom_filter') ? $request->query->get('dateDeVigueurFrom_filter') : "";
            $this->dateDeVigueurTo_filter = $request->query->has('dateDeVigueurTo_filter') ? $request->query->get('dateDeVigueurTo_filter') : "";
            $this->code_filter = $request->query->has('code_filter') ? $request->query->get('code_filter') : "";
            $this->contenu_filter = $request->query->has('contenu_filter') ? $request->query->get('contenu_filter') : "";
            $this->etat_filter = $request->query->has('etat_filter') ? $request->query->get('etat_filter') : "";
            $this->reference_filter = $request->query->has('reference_filter') ? $request->query->get('reference_filter') : "";
            $this->dateFinFrom_filter = $request->query->has('dateFinFrom_filter') ? $request->query->get('dateFinFrom_filter') : "";
            $this->dateFinTo_filter = $request->query->has('dateFinTo_filter') ? $request->query->get('dateFinTo_filter') : "";
            $this->date_filter = $request->query->has('date_filter') ? $request->query->get('date_filter') : "";
            $this->type_filter = $request->query->has('type_filter') ? $request->query->get('type_filter') : "";
            $this->valide_filter = $request->query->has('valide_filter') ? $request->query->get('valide_filter') : "";
            $this->emetteur_filter = $request->query->has('emetteur_filter') ? $request->query->get('emetteur_filter') : "";
            $this->recepteur_filter = $request->query->has('recepteur_filter') ? $request->query->get('recepteur_filter') : "";
            $iDisplayLength = $request->query->has('length') ? $request->query->get('length') : -1;
            $iDisplayStart = $request->query->has('start') ? intval($request->query->get('start')) : 0;
            $json = array();
            $json['items'] = array();
            $q = $request->query->has('q') ? $request->query->get('q') : "all";
            if ($q === "sender" || $q == "all") {
                $communicationsSent = $user->getEmetteurCommunications();
                $iTotalRecords = count($communicationsSent);
                if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
                $communicationsSent = $this->handleResults($communicationsSent, $iTotalRecords, $iDisplayStart, $iDisplayLength);
                $iFilteredRecords = count($communicationsSent);
                $data = $this->get('apm_core.data_serialized')->getFormalData($communicationsSent, array("owner_list"));
                $json['totalRecordsSent'] = $iTotalRecords;
                $json['filteredRecordsSent'] = $iFilteredRecords;
                $json['items']['sender'] = $data;
            }

            if ($q === "receiver" || $q === "all") {
                $communicationsReceived = $user->getRecepteurCommunications();
                $iTotalRecords = count($communicationsReceived);
                if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
                $communicationsReceived = $this->handleResults($communicationsReceived, $iTotalRecords, $iDisplayStart, $iDisplayLength);
                $iFilteredRecords = count($communicationsReceived);
                $data = $this->get('apm_core.data_serialized')->getFormalData($communicationsReceived, array("owner_list"));
                $json['totalRecordsReceived'] = $iTotalRecords;
                $json['filteredRecordsReceived'] = $iFilteredRecords;
                $json['items']['receiver'] = $data;
            }
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

    private function listAndShowSecurity()
    {
        //-----------------------------------security-------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @param Collection $communications
     * @param $iTotalRecords
     * @param $iDisplayStart
     * @param $iDisplayLength
     * @return array
     */
    private function handleResults($communications, $iTotalRecords, $iDisplayStart, $iDisplayLength)
    {
        //filtering
        if ($communications === null) return array();

        if ($this->code_filter != null) {
            $communications = $communications->filter(function ($e) {//filtrage select
                /** @var Communication $e */
                return $e->getCode() === $this->code_filter;
            });
        }
        if ($this->etat_filter != null) {
            $communications = $communications->filter(function ($e) {//filtrage select
                /** @var Communication $e */
                return $e->getEtat() === $this->etat_filter;
            });
        }
        if ($this->type_filter != null) {
            $communications = $communications->filter(function ($e) {//filtrage select
                /** @var Communication $e */
                return $e->getType() === $this->type_filter;
            });
        }
        if ($this->valide_filter != null) {
            $communications = $communications->filter(function ($e) {//filtrage select
                /** @var Communication $e */
                return $e->getValide() === boolval($this->valide_filter);
            });
        }
        if ($this->dateDeVigueurFrom_filter != null) {
            $communications = $communications->filter(function ($e) {//start date
                /** @var Communication $e */
                $dt1 = (new \DateTime($e->getDateDeVigueur()->format('d-m-Y')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateDeVigueurFrom_filter))->getTimestamp();
                return $dt1 - $dt2 >= 0 ? true : false; //start from the given date 'dateFrom_filter'
            });
        }
        if ($this->dateDeVigueurTo_filter != null) {
            $communications = $communications->filter(function ($e) {//end date
                /** @var Communication $e */
                $dt = (new \DateTime($e->getDateDeVigueur()->format('d-m-Y')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateDeVigueurTo_filter))->getTimestamp();
                return $dt - $dt2 <= 0 ? true : false;// end from at the given date 'dateTo_filter'
            });
        }
        if ($this->dateFinFrom_filter != null) {
            $communications = $communications->filter(function ($e) {//start date
                /** @var Communication $e */
                $dt1 = (new \DateTime($e->getDateFin()->format('d-m-Y')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateFinFrom_filter))->getTimestamp();
                return $dt1 - $dt2 >= 0 ? true : false; //start from the given date 'dateFrom_filter'
            });
        }
        if ($this->dateFinTo_filter != null) {
            $communications = $communications->filter(function ($e) {//end date
                /** @var Communication $e */
                $dt = (new \DateTime($e->getDateFin()->format('d-m-Y')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateFinTo_filter))->getTimestamp();
                return $dt - $dt2 <= 0 ? true : false;// end from at the given date 'dateTo_filter'
            });
        }
        if ($this->emetteur_filter != null) {
            $communications = $communications->filter(function ($e) {//search for occurences in the text
                /** @var Communication $e */
                $subject = $e->getEmetteur();
                $pattern = $this->emetteur_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }
        if ($this->recepteur_filter != null) {
            $communications = $communications->filter(function ($e) {//search for occurences in the text
                /** @var Communication $e */
                $subject = $e->getRecepteur();
                $pattern = $this->recepteur_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }

        $communications = ($communications !== null) ? $communications->toArray() : [];
        //assortment: descending of date -- du plus recent au plus ancient
        usort(
            $communications, function ($e1, $e2) {
            /**
             * @var Communication $e1
             * @var Communication $e2
             */
            $dt1 = $e1->getDate()->getTimestamp();
            $dt2 = $e2->getDate()->getTimestamp();
            return $dt1 <= $dt2 ? 1 : -1;
        });
        if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
        //paging; slice and preserve keys' order
        $communications = array_slice($communications, $iDisplayStart, $iDisplayLength, true);

        return $communications;
    }

    /**
     * @ApiDoc(
     * resource=true,
     * resourceDescription="Operations on Communication.",
     * description="Create an object of type Communication.",
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
     *    "class"="APM\UserBundle\Entity\Communication",
     *     "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\ValidationParser"
     *      },
     *    "name" = "Communication",
     * },
     * views = {"default", "user" }
     * )
     * @param Request $request
     * @return View | JsonResponse
     *
     * @Post("/new/communication")
     */
    public function newAction(Request $request)
    {
        try {
            $this->createSecurity();
            /** @var Communication $communication */
            $communication = TradeFactory::getTradeProvider("communication");
            $form = $this->createForm('APM\UserBundle\Form\CommunicationType', $communication);
            $form->submit($request->request->all());
            if (!$form->isValid()) {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $communication->setEmetteur($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($communication);
            $em->flush();
            return $this->routeRedirectView("api_user_show_communication", ['id' => $communication->getId()], Response::HTTP_CREATED);
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

    private function createSecurity()
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');

        /* ensure that the user is logged in */
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @ApiDoc(
     * resource=true,
     * description="Retrieve the details of an objet of type Communication.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * requirements = {
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="communication id"}
     * },
     * output={
     *   "class"="APM\UserBundle\Entity\Communication",
     *   "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *    },
     *     "groups"={"owner_communication_details", "owner_list"}
     * },
     * statusCodes={
     *     "output" = "A single Object",
     *     200="Returned when successful",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "user"}
     * )
     * @param Communication $communication
     * @return JsonResponse
     *
     * @Get("/show/communication/{id}")
     */
    public function showAction(Communication $communication)
    {
        $this->listAndShowSecurity();
        $data = $this->get('apm_core.data_serialized')->getFormalData($communication, ["owner_communication_details", "owner_list"]);
        return new JsonResponse($data, 200);
    }

    /**
     * @ApiDoc(
     * resource=true,
     * resourceDescription="Operations on Communication",
     * description="Update an object of type Communication.",
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
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="communication Id"}
     * },
     * input={
     *    "class"="APM\UserBundle\Entity\Communication",
     *     "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\ValidationParser"
     *      },
     *    "name" = "Communication",
     * },
     *
     * views = {"default", "user" }
     * )
     * @param Request $request
     * @param Communication $communication
     * @return View | JsonResponse
     *
     * @Put("/edit/communication/{id}")
     */
    public function editAction(Request $request, Communication $communication)
    {
        try {
            $this->editAndDeleteSecurity($communication);
            $form = $this->createForm('APM\UserBundle\Form\CommunicationType', $communication);
            $form->submit($request->request->all(), false);
            if (!$form->isValid()) {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->routeRedirectView("api_user_show_communication", ['id' => $communication->getId()], Response::HTTP_OK);
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
     * @param Communication $communication
     */
    private function editAndDeleteSecurity($communication)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        $user = $this->getUser();
        if ((!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) || ($communication->getEmetteur() !== $user)) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------

    }

    /**
     * @ApiDoc(
     * resource=true,
     * description="Delete objet of type Communication.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * requirements = {
     *      {"name"="id", "dataType"="integer", "required"=true, "requirement"="\d+", "description"="communication Id"}
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
     *     views={"default", "user"}
     * )
     * @param Request $request
     * @param Communication $communication
     * @return View | JsonResponse
     *
     * @Delete("/delete/communication/{id}")
     */
    public function deleteAction(Request $request, Communication $communication)
    {
        try {
            $this->editAndDeleteSecurity($communication);
            if (!$request->request->has('exec') || $request->request->get('exec') !== 'go') {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans('impossible de supprimer', [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $em = $this->getDoctrine()->getManager();
            $em->remove($communication);
            $em->flush();
            return $this->routeRedirectView("api_user_get_communications", [], Response::HTTP_OK);
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
