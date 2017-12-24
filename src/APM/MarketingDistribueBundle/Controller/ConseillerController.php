<?php

namespace APM\MarketingDistribueBundle\Controller;

use APM\MarketingDistribueBundle\Entity\Conseiller;
use APM\MarketingDistribueBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Patch;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Conseiller controller.
 * @RouteResource("conseiller", pluralize=false)
 */
class ConseillerController extends FOSRestController
{
    private $matricule_filter;
    private $code_filter;
    private $dateFrom_filter;
    private $dateTo_filter;
    private $valeurQuota_filter;
    private $description_filter;
    private $dateCreationReseauFrom_filter;
    private $dateCreationReseauTo_filter;
    private $isConseillerA2;

    /**
     * @ApiDoc(
     * resource=true,
     * description="Retrieve list of Conseillers.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * filters={
     *      {"name"="code_filter", "dataType"="string"},
     *      {"name"="dateFrom_filter", "dataType"="dateTime", "pattern"="19-12-2017|ASC"},
     *      {"name"="dateTo_filter", "dataType"="dateTime", "pattern"="19-12-2017|DESC"},
     *      {"name"="dateCreationReseauFrom_filter", "dataType"="dateTime", "pattern"="19-12-2017|ASC"},
     *      {"name"="dateCreationReseauTo_filter", "dataType"="dateTime", "pattern"="19-12-2017|DESC"},
     *      {"name"="matricule_filter", "dataType"="string"},
     *      {"name"="description_filter", "dataType"="string"},
     *      {"name"="valeurQuota_filter", "dataType"="integer"},
     *      {"name"="length_filter", "dataType"="integer", "requirement"="\d+"},
     *      {"name"="start_filter", "dataType"="integer", "requirement"="\d+"},
     *  },
     *
     * output={
     *   "class"="APM\MarketingDistribueBundle\Entity\Conseiller",
     *   "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *    },
     *     "groups"={"owner_list"}
     * },
     * statusCodes={
     *     "output" = "A single or a collection of Conseiller",
     *     200="Returned when successful",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "marketing"}
     * )
     * @return JsonResponse
     *
     * @Get("/cget/conseillers", name="s")
     */
    public function getAction()
    {
        try {
            $this->listAndShowSecurity();
            $em = $this->getDoctrine()->getManager();
            $conseillers = $em->getRepository('APMMarketingDistribueBundle:conseiller')->findAll();
            $json = array();
            $this->matricule_filter = $request->request->has('matricule_filter') ? $request->request->get('matricule_filter') : "";
            $this->code_filter = $request->request->has('code_filter') ? $request->request->get('code_filter') : "";
            $this->dateFrom_filter = $request->request->has('dateFrom_filter') ? $request->request->get('dateFrom_filter') : "";
            $this->dateTo_filter = $request->request->has('dateTo_filter') ? $request->request->get('dateTo_filter') : "";
            $this->valeurQuota_filter = $request->request->has('valeurQuota_filter') ? $request->request->get('valeurQuota_filter') : "";
            $this->description_filter = $request->request->has('description_filter') ? $request->request->get('description_filter') : "";
            $this->dateCreationReseauFrom_filter = $request->request->has('dateCreationReseauFrom_filter') ? $request->request->get('dateCreationReseauFrom_filter') : "";
            $this->dateCreationReseauTo_filter = $request->request->has('dateCreationReseauTo_filter') ? $request->request->get('dateCreationReseauTo_filter') : "";
            $iDisplayLength = $request->request->has('length') ? $request->request->get('length') : -1;
            $iDisplayStart = $request->request->has('start') ? intval($request->request->get('start')) : 0;
            $iTotalRecords = count($conseillers);
            if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
            $_conseillers = new ArrayCollection();
            foreach ($conseillers as $conseiller) {
                $_conseillers->add($conseiller);
            }
            $conseillers = $this->handleResults($_conseillers, $iTotalRecords, $iDisplayStart, $iDisplayLength);
            $iFilteredRecords = count($conseillers);
            $data = $this->get('apm_core.data_serialized')->getFormalData($conseillers, array("others_list"));
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

    private function listAndShowSecurity()
    {
        //-----------------------------------security-------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @param Collection $conseillers
     * @param $iTotalRecords
     * @param $iDisplayStart
     * @param $iDisplayLength
     * @return array
     */
    private function handleResults($conseillers, $iTotalRecords, $iDisplayStart, $iDisplayLength)
    {
        //filtering
        if ($conseillers === null) return array();

        if ($this->code_filter != null) {
            $conseillers = $conseillers->filter(function ($e) {
                /** @var Conseiller $e */
                return $e->getCode() === $this->code_filter;
            });
        }
        if ($this->matricule_filter != null) {
            $conseillers = $conseillers->filter(function ($e) {
                /** @var Conseiller $e */
                return $e->getMatricule() === $this->matricule_filter;
            });
        }
        if ($this->isConseillerA2 != null) {
            $conseillers = $conseillers->filter(function ($e) {
                /** @var Conseiller $e */
                return $e->getConseillerA2() === boolval($this->isConseillerA2);
            });
        }

        if ($this->dateFrom_filter != null) {
            $conseillers = $conseillers->filter(function ($e) {//start date
                /** @var Conseiller $e */
                $dt1 = (new \DateTime($e->getDateEnregistrement()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateFrom_filter))->getTimestamp();
                return $dt1 - $dt2 >= 0 ? true : false; //start from the given date 'dateFrom_filter'
            });
        }
        if ($this->dateTo_filter != null) {
            $conseillers = $conseillers->filter(function ($e) {//end date
                /** @var Conseiller $e */
                $dt = (new \DateTime($e->getDateEnregistrement()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateTo_filter))->getTimestamp();
                return $dt - $dt2 <= 0 ? true : false;// end from at the given date 'dateTo_filter'
            });
        }
        if ($this->dateFrom_filter != null) {
            $conseillers = $conseillers->filter(function ($e) {//start date
                /** @var Conseiller $e */
                $dt1 = (new \DateTime($e->getDateCreationReseau()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateFrom_filter))->getTimestamp();
                return $dt1 - $dt2 >= 0 ? true : false; //start from the given date 'dateFrom_filter'
            });
        }
        if ($this->dateTo_filter != null) {
            $conseillers = $conseillers->filter(function ($e) {//end date
                /** @var Conseiller $e */
                $dt = (new \DateTime($e->getDateCreationReseau()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateTo_filter))->getTimestamp();
                return $dt - $dt2 <= 0 ? true : false;// end from at the given date 'dateTo_filter'
            });
        }
        if ($this->description_filter != null) {
            $conseillers = $conseillers->filter(function ($e) {//search for occurences in the text
                /** @var Conseiller $e */
                $subject = $e->getDescription();
                $pattern = $this->description_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }
        $conseillers = ($conseillers !== null) ? $conseillers->toArray() : [];
        //assortment: descending of date -- du plus recent au plus ancient
        usort(
            $conseillers, function ($e1, $e2) {
            /**
             * @var Conseiller $e1
             * @var Conseiller $e2
             */
            $dt1 = $e1->getDateEnregistrement()->getTimestamp();
            $dt2 = $e2->getDateEnregistrement()->getTimestamp();
            return $dt1 <= $dt2 ? 1 : -1;
        });

        if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
        //paging; slice and preserve keys' order
        $conseillers = array_slice($conseillers, $iDisplayStart, $iDisplayLength, true);

        return $conseillers;
    }

    /**
     * @ApiDoc(
     * resource=true,
     * resourceDescription="Operations on Conseiller",
     * description="Create an object of type Conseiller",
     * statusCodes={
     *         201="Returned when successful",
     *         400="Returned when the data are not valid or an unknown error occurred",
     *         403="Returned when the user is not authorized to carry on the action",
     *         404="Returned when the entity is not found",
     * },
     * headers={
     *      { "name"="Authorization", "required"=true, "description"="Authorization token"}
     * },
     * input={
     *    "class"="APM\MarketingDistribueBundle\Entity\Conseiller",
     *     "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\ValidationParser"
     *      },
     *    "name" = "Conseiller",
     * },
     *  views = {"default", "marketing" }
     * )
     * @param Request $request
     * @return View | JsonResponse
     *
     * @Post("/new/conseiller")
     */
    public function newAction(Request $request)
    {
        try {
            $this->createSecurity();
            /** @var Conseiller $conseiller */
            $conseiller = TradeFactory::getTradeProvider("conseiller");
            $form = $this->createForm('APM\MarketingDistribueBundle\Form\ConseillerType', $conseiller);
            $form->submit($request->request->all());
            if (!$form->isValid()) {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }

            $conseiller->setUtilisateur($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($conseiller);
            $em->flush();

            return $this->routeRedirectView("api_marketing_show_conseiller", ['id' => $conseiller->getId()], Response::HTTP_CREATED);

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
        // Unable to access the controller unless you have a CONSEILLER role
        $this->denyAccessUnlessGranted('ROLE_CONSEILLER', null, 'Unable to access this page!');
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') || !$user->isConseillerA1()) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @ApiDoc(
     * resource=true,
     * description="Retrieve the details of an objet of type Conseiller",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * requirements = {
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="conseiller_id"}
     * },
     * output={
     *   "class"="APM\MarketingDistribueBundle\Entity\Conseiller",
     *   "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *    },
     *     "groups"={"owner_conseiller_details", "owner_list"}
     * },
     * statusCodes={
     *     "output" = "A single Object",
     *     200="Returned when successful",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "marketing"}
     * )
     * @param Conseiller $conseiller
     * @return JsonResponse
     *
     * @Get("/show/conseiller/{id}")
     */
    public function showAction(Conseiller $conseiller)
    {
        $this->listAndShowSecurity();
        $data = $this->get('apm_core.data_serialized')->getFormalData($conseiller, ["owner_conseiller_details", "owner_list"]);
        return new JsonResponse($data, 200);
    }

    /**
     * @ApiDoc(
     * resource=true,
     * resourceDescription="Operations on conseiller",
     * description="Update an object of type Conseiller.",
     * statusCodes={
     *         200="Returned when successful",
     *         400="Returned when the data are not valid or an unknown error occurred",
     *         403="Returned when the user is not authorized to carry on the action",
     *         404="Returned when the entity is not found",
     * },
     * headers={
     *      { "name"="Authorization", "required"=true, "description"="Authorization token"}
     * },
     * requirements = {
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="conseiller Id"}
     * },
     * input={
     *    "class"="APM\MarketingDistribueBundle\Entity\Conseiller",
     *     "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\ValidationParser"
     *      },
     *    "name" = "Conseiller",
     * },
     *     views={"default","marketing"}
     * )
     * @param Request $request
     * @param Conseiller $conseiller
     * @return View | JsonResponse
     *
     * @Post("/edit/conseiller/{id}")
     */
    public function editAction(Request $request, Conseiller $conseiller)
    {
        try {
            $this->editAndDeleteSecurity($conseiller);
            $form = $this->createForm('APM\MarketingDistribueBundle\Form\ConseillerType', $conseiller);
            $form->submit($request->request->all(), false);
            if (!$form->isValid()) {
                return new JsonResponse(
                    [
                        "status" => 400,
                        "message" => $this->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                    ], Response::HTTP_BAD_REQUEST
                );
            }
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->routeRedirectView("api_marketing_show_conseiller", ['id' => $conseiller->getId()], Response::HTTP_OK);

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
     * @param Conseiller $conseiller
     */
    private function editAndDeleteSecurity($conseiller)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a CONSEILLER role
        $this->denyAccessUnlessGranted('ROLE_CONSEILLER', null, 'Unable to access this page!');
        $user = $this->getUser();
        if ((!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) ||
            ($conseiller->getUtilisateur() !== $user)
        ) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }


    /**
     * @ApiDoc(
     * resource=true,
     * description="Delete objet of type Conseiller",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * requirements = {
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="conseiller Id"}
     * },
     * parameters = {
     *      {"name"="exec", "required"=true, "dataType"="string", "requirement"="\D+", "description"="needed to check the origin of the request", "format"="exec=go"}
     * },
     * statusCodes={
     *     200="Returned when successful",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "marketing"}
     * )
     * @param Request $request
     * @param Conseiller $conseiller
     * @return View | JsonResponse
     *
     * @Delete("/delete/conseiller/{id}")
     */
    public function deleteAction(Request $request, Conseiller $conseiller)
    {
        try {
            $this->editAndDeleteSecurity($conseiller);
            if (!$request->request->has('exec') || $request->request->get('exec') !== 'go') {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans('impossible de supprimer', [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $user = $conseiller->getUtilisateur();
            $em = $this->getDoctrine()->getManager();
            $em->remove($conseiller);
            $em->flush();
            return $this->routeRedirectView("api_user_show", [$user->getId()], Response::HTTP_OK);
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
