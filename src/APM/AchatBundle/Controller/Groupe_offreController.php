<?php

namespace APM\AchatBundle\Controller;

use APM\AchatBundle\Entity\Groupe_offre;
use APM\AchatBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Patch;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Groupe_offre controller.
 * Liste les Groupe d'offre crees par l'utilisateur
 * @RouteResource("groupeoffre",  pluralize=false)
 */
class Groupe_offreController extends FOSRestController
{
    private $code_filter;
    private $propriete_filter;
    private $designation_filter;
    private $dateFrom_filter;
    private $dateTo_filter;
    private $dateVigueurFrom_filter;
    private $dateVigueurTo_filter;
    private $description_filter;
    private $createur_filter;
    private $recurrent_filter;


    /**
     * @ApiDoc(
     * resource=true,
     * description="Retrieve list of groupe offre.",
     * headers={
     *    { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * filters={
     *      {"name"="propriete_filter", "dataType"="integer", "pattern"="1,2,3|SELECT"},
     *      {"name"="code_filter", "dataType"="string"},
     *      {"name"="designation_filter", "dataType"="string"},
     *      {"name"="dateFrom_filter", "dataType"="dateTime", "pattern"="19-12-2017|ASC"},
     *      {"name"="dateTo_filter", "dataType"="dateTime", "pattern"="19-12-2017|DESC"},
     *      {"name"="dateVigueurFrom_filter", "dataType"="dateTime", "pattern"="19-12-2017|ASC"},
     *      {"name"="dateVigueurTo_filter", "dataType"="dateTime", "pattern"="19-12-2017|DESC"},
     *      {"name"="description_filter", "dataType"="string"},
     *      {"name"="recurrent_filter", "dataType"="boolean"},
     *      {"name"="createur_filter", "dataType"="string", "pattern"="yannick|USERNAME"},
     *      {"name"="length_filter", "dataType"="integer", "requirement"="\d+"},
     *      {"name"="start_filter", "dataType"="integer", "requirement"="\d+"},
     *  },
     *
     * output={
     *   "class"="APM\AchatBundle\Entity\Groupe_offre",
     *   "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *    },
     *     "groups"={"owner_list"}
     * },
     * statusCodes={
     *     "output" = "A single or a collection of Groupe_offre",
     *     200="Returned when successful",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "achat"}
     * )
     * @param Request $request
     * @return JsonResponse
     *
     * @Get("/cget/collectionoffres", name="s")
     */
    public function getAction(Request $request)
    {
        try {
            $this->listAndShowSecurity();
            /** @var Utilisateur_avm $user */
            $user = $this->getUser();
            $groupe_offres = $user->getGroupesOffres();//liste
            $this->propriete_filter = $request->query->has('propriete_filter') ? $request->query->get('propriete_filter') : "";
            $this->code_filter = $request->query->has('code_filter') ? $request->query->get('code_filter') : "";
            $this->designation_filter = $request->query->has('designation_filter') ? $request->query->get('designation_filter') : "";
            $this->dateFrom_filter = $request->query->has('dateFrom_filter') ? $request->query->get('dateFrom_filter') : "";
            $this->dateTo_filter = $request->query->has('dateTo_filter') ? $request->query->get('dateTo_filter') : "";
            $this->dateVigueurFrom_filter = $request->query->has('dateVigueurFrom_filter') ? $request->query->get('dateVigueurFrom_filter') : "";
            $this->dateVigueurTo_filter = $request->query->has('dateVigueurTo_filter') ? $request->query->get('dateVigueurTo_filter') : "";
            $this->description_filter = $request->query->has('description_filter') ? $request->query->get('description_filter') : "";
            $this->createur_filter = $request->query->has('createur_filter') ? $request->query->get('createur_filter') : "";
            $this->recurrent_filter = $request->query->has('recurrent_filter') ? $request->query->get('recurrent_filter') : "";

            $iDisplayLength = $request->query->has('length') ? $request->query->get('length') : -1;
            $iDisplayStart = $request->query->has('start') ? intval($request->query->get('start')) : 0;
            $json = array();
            $iTotalRecords = count($groupe_offres);
            if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
            $groupe_offres = $this->handleResults($groupe_offres, $iTotalRecords, $iDisplayStart, $iDisplayLength);
            $iFilteredRecords = count($groupe_offres);
            $data = $this->get('apm_core.data_serialized')->getFormalData($groupe_offres, array("owner_list"));
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

    private function listAndShowSecurity(Groupe_offre $groupe_offre = null)
    {
        //-----------------------------------security-------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw $this->createAccessDeniedException();
        }
        if ($groupe_offre !== null) {
            if ($this->getUser() !== $groupe_offre->getCreateur()) {
                throw $this->createAccessDeniedException();
            }
        }
        //------------------------------------------------------------------------------
    }

    /**
     * @param Collection $groupe_offres
     * @param $iTotalRecords
     * @param $iDisplayStart
     * @param $iDisplayLength
     * @return array
     */
    private function handleResults($groupe_offres, $iTotalRecords, $iDisplayStart, $iDisplayLength)
    {
        //filtering
        if ($groupe_offres === null) return array();

        if ($this->code_filter != null) {
            $groupe_offres = $groupe_offres->filter(function ($e) {//filtrage select
                /** @var Groupe_offre $e */
                return $e->getCode() === $this->code_filter;
            });
        }
        if ($this->propriete_filter != null) {
            $groupe_offres = $groupe_offres->filter(function ($e) {//filtrage select
                /** @var Groupe_offre $e */
                return $e->getPropriete() === intval($this->propriete_filter);
            });
        }
        if ($this->recurrent_filter != null) {
            $groupe_offres = $groupe_offres->filter(function ($e) {//filtrage select
                /** @var Groupe_offre $e */
                return $e->getRecurrent() === boolval($this->recurrent_filter);
            });
        }
        if ($this->createur_filter != null) {
            $groupe_offres = $groupe_offres->filter(function ($e) {//filter with the begining of the entering word
                /** @var Groupe_offre $e */
                $str1 = $e->getCreateur()->getCode();
                $str2 = $this->createur_filter;
                return strcasecmp($str1, $str2) === 0 ? true : false;
            });
        }
        if ($this->description_filter != null) {
            $groupe_offres = $groupe_offres->filter(function ($e) {//search for occurences in the text
                /** @var Groupe_offre $e */
                $subject = $e->getDescription();
                $pattern = $this->description_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }
        if ($this->designation_filter != null) {
            $groupe_offres = $groupe_offres->filter(function ($e) {//search for occurences in the text
                /** @var Groupe_offre $e */
                $subject = $e->getDesignation();
                $pattern = $this->designation_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }
        if ($this->dateFrom_filter != null) {
            $groupe_offres = $groupe_offres->filter(function ($e) {//start date
                /** @var Groupe_offre $e */
                $dt1 = (new \DateTime($e->getDateCreation()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateFrom_filter))->getTimestamp();
                return $dt1 - $dt2 >= 0 ? true : false; //start from the given date 'dateFrom_filter'
            });
        }
        if ($this->dateTo_filter != null) {
            $groupe_offres = $groupe_offres->filter(function ($e) {//end date
                /** @var Groupe_offre $e */
                $dt = (new \DateTime($e->getDateCreation()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateTo_filter))->getTimestamp();
                return $dt - $dt2 <= 0 ? true : false;// end from at the given date 'dateTo_filter'
            });
        }
        if ($this->dateVigueurFrom_filter != null) {
            $groupe_offres = $groupe_offres->filter(function ($e) {//start date
                /** @var Groupe_offre $e */
                $dt1 = (new \DateTime($e->getDateDeVigueur()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateVigueurFrom_filter))->getTimestamp();
                return $dt1 - $dt2 >= 0 ? true : false; //start from the given date 'dateFrom_filter'
            });
        }
        if ($this->dateVigueurTo_filter != null) {
            $groupe_offres = $groupe_offres->filter(function ($e) {//end date
                /** @var Groupe_offre $e */
                $dt = (new \DateTime($e->getDateDeVigueur()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateVigueurTo_filter))->getTimestamp();
                return $dt - $dt2 <= 0 ? true : false;// end from at the given date 'dateTo_filter'
            });
        }

        $groupe_offres = ($groupe_offres !== null) ? $groupe_offres->toArray() : [];
        //assortment: descending of date -- du plus recent au plus ancient
        usort(
            $groupe_offres, function ($e1, $e2) {
            /**
             * @var Groupe_offre $e1
             * @var Groupe_offre $e2
             */
            $dt1 = $e1->getDateDeVigueur()->getTimestamp();
            $dt2 = $e2->getDateDeVigueur()->getTimestamp();
            return $dt1 <= $dt2 ? 1 : -1;
        });
        if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
        //paging; slice and preserve keys' order
        $groupe_offres = array_slice($groupe_offres, $iDisplayStart, $iDisplayLength, true);

        return $groupe_offres;
    }

    /**
     * @ApiDoc(
     * resource=true,
     * resourceDescription="Operations on groupeOffre.",
     * description="Create an object of type groupe offre.",
     * statusCodes={
     *         201="The details are returned when successful",
     *         400="Returned when the data are not valid or an unknown error occurred",
     *         403="Returned when the user is not authorized to carry on the action",
     *         404="Returned when the entity is not found",
     * },
     * headers={
     *      { "name"="Authorization",  "required"=true, "description"="Authorization token"}
     * },
     * authentication= true,
     * authenticationRoles= {
     *          "ROLE_USERAVM"
     *     },
     * input={
     *     "class"="APM\AchatBundle\Form\Groupe_offreType",
     *     "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\FormTypeParser"
     *      }
     * },
     * output={
     *   "class"="APM\AchatBundle\Entity\Groupe_offre",
     *   "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *    },
     *     "groups"={"owner_groupeO_details", "owner_list"}
     * },
     * views = {"default", "achat" }
     * )
     * @param Request $request
     * @return JsonResponse|View

     * @Post("/new/collectionoffre")
     */
    public function newAction(Request $request)
    {
        try {
            $this->createSecurity();
            /** @var Groupe_offre $groupe_offre */
            $groupe_offre = TradeFactory::getTradeProvider("groupe_offre");
            $form = $this->createForm('APM\AchatBundle\Form\Groupe_offreType', $groupe_offre);
            $data = $request->request->has($form->getName()) ? $request->request->get($form->getName()) : $data[$form->getName()] = array();
            $form->submit($data);
            if (!$form->isValid()) {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }

            $groupe_offre->setCreateur($this->getUser());
            $em = $this->getEM();
            $em->persist($groupe_offre);
            $em->flush();

            return $this->routeRedirectView("api_achat_show_groupeoffre", ['id' => $groupe_offre->getId()], Response::HTTP_CREATED);

            /*} catch (ConstraintViolationException $cve) {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans("impossible d'enregistrer, vérifiez vos données", [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);*/
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
        /* ensure that the user is logged in
        */
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    private function getEM()
    {
        return $this->get('doctrine.orm.entity_manager');
    }

    /**
     * @ApiDoc(
     * resource=true,
     * resourceDescription="Operations on groupeOffre.",
     * description="Update an object of type groupe offre.",
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
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="groupe offre Id"}
     * },
     * authentication= true,
     * authenticationRoles= {
     *          "ROLE_USERAVM"
     *     },
     * input={
     *     "class"="APM\AchatBundle\Form\Groupe_offreType",
     *     "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\FormTypeParser"
     *      }
     * },
     * views = {"default", "achat" }
     * )
     * @param Request $request
     * @param Groupe_offre $groupe_offre
     * @return JsonResponse | View
     * @Put("/edit/collectionoffre/{id}")
     */
    public function editAction(Request $request, Groupe_offre $groupe_offre)
    {
        try {
            $this->editAndDeleteSecurity($groupe_offre);
            $form = $this->createForm('APM\AchatBundle\Form\Groupe_offreType', $groupe_offre);
            $data = $request->request->has($form->getName()) ? $request->request->get($form->getName()) : $data[$form->getName()] = array();
            $form->submit($data, false);
            if (!$form->isValid()) {
                return new JsonResponse(
                    [
                        "status" => 400,
                        "message" => $this->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                    ], Response::HTTP_BAD_REQUEST
                );
            }
            $em = $this->getEM();
            $em->flush();
            return new JsonResponse(['status' => 200], Response::HTTP_OK);

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
     * @param Groupe_offre $groupe_offre
     */
    private function editAndDeleteSecurity($groupe_offre)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        if ($groupe_offre) {
            $user = $this->getUser();
            if ($groupe_offre->getCreateur() !== $user) {
                throw $this->createAccessDeniedException();
            }
        }
        //----------------------------------------------------------------------------------------

    }

    /**
     * @ApiDoc(
     * resource=true,
     * description="Retrieve the details of an objet of type groupe offre.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * requirements = {
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="groupe offre Id"}
     * },
     * output={
     *   "class"="APM\AchatBundle\Entity\Groupe_offre",
     *   "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *    },
     *     "groups"={"owner_groupeO_details", "owner_list"}
     * },
     * statusCodes={
     *     "output" = "A single Object",
     *     200="Returned when successful",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "achat"}
     * )
     * @param Groupe_offre $groupe_offre
     * @return JsonResponse
     * @Get("/show/collectionoffre/{id}")
     */
    public function showAction(Groupe_offre $groupe_offre)
    {
        $this->listAndShowSecurity();
        $data = $this->get('apm_core.data_serialized')->getFormalData($groupe_offre, ["owner_groupeO_details", "owner_list"]);
        return new JsonResponse($data, 200);
    }

    /**
     * @ApiDoc(
     * resource=true,
     * description="Delete objet of type groupe offre.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * requirements = {
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="groupe offre Id"}
     * },
     * parameters = {
     *      {"name"="exec", "required"=true, "dataType"="string", "requirement"="\D+", "description"="needed to check the origin of the request", "format"="exec=go"}
     * },
     * statusCodes={
     *     200="Returned when successful",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "achat"}
     * )
     * @param Request $request
     * @param Groupe_offre $groupe_offre
     * @return JsonResponse| View
     * @Delete("/delete/collectionoffre/{id}")
     */
    public function deleteAction(Request $request, Groupe_offre $groupe_offre)
    {
        try {
            $this->editAndDeleteSecurity($groupe_offre);
            if (!$request->request->has('exec') || $request->request->get('exec') !== 'go') {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans('impossible de supprimer', [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $em = $this->getEM();
            $em->remove($groupe_offre);
            $em->flush();
            return new JsonResponse(['status' => 200], Response::HTTP_OK);

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
