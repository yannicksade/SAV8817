<?php

namespace APM\UserBundle\Controller;

use APM\UserBundle\Entity\Groupe_relationnel;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\UserBundle\Factory\TradeFactory;
use Doctrine\Common\Collections\ArrayCollection;
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
 * Groupe_relationnel controller.
 * @RouteResource("groupeRelationnel", pluralize=false)
 */
class Groupe_relationnelController extends FOSRestController
{
    private $dateCreationFrom_filter;
    private $dateCreationTo_filter;
    private $code_filter;
    private $description_filter;
    private $designation_filter;
    private $conversationalGroup_filter;
    private $type_filter;
    private $proprietaire_filter;
    private $boutique_filter;

    /**
     * @ApiDoc(
     * resource=true,
     * description="Retrieve list of groupe_relationnels.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * filters={
     *      {"name"="code_filter", "dataType"="string"},
     *      {"name"="designation_filter", "dataType"="string"},
     *      {"name"="dateCreationFrom_filter", "dataType"="dateTime", "pattern"="19-12-2017|ASC"},
     *      {"name"="dateCreationTo_filter", "dataType"="dateTime", "pattern"="19-12-2017|DESC"},
     *      {"name"="conversationalGroup_filter", "dataType"="boolean"},
     *      {"name"="type_filter", "dataType"="integer"},
     *      {"name"="description_filter", "dataType"="string"},
     *      {"name"="proprietaire_filter", "dataType"="string"},
     *      {"name"="length", "dataType"="integer", "requirement"="\d+"},
     *      {"name"="start", "dataType"="integer", "requirement"="\d+"},
     *  },
     * parameters={
     *   {"name"="q", "dataType"="string", "required"=false, "description"="query: OWNER, GUEST", "format"="?q=owner"}
     * },
     * output={
     *   "class"="APM\UserBundle\Entity\Groupe_relationnel",
     *   "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *    },
     *     "groups"={"owner_list"}
     * },
     * statusCodes={
     *     "output" = "A single or a collection of Groupe_relationnel",
     *     200="Returned when successful",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "user"}
     * )
     * @param Request $request
     * @return JsonResponse
     *
     * @Get("/cget/groupeRelationnels", name="s")
     */
    public function getAction(Request $request)
    {
        try {
            $this->listAndShowSecurity();
            /** @var Utilisateur_avm $user */
            $user = $this->getUser();
            $this->dateCreationFrom_filter = $request->request->has('dateCreationFrom_filter') ? $request->request->get('dateCreationFrom_filter') : "";
            $this->dateCreationTo_filter = $request->request->has('dateCreationTo_filter') ? $request->request->get('dateCreationTo_filter') : "";
            $this->code_filter = $request->request->has('code_filter') ? $request->request->get('code_filter') : "";
            $this->description_filter = $request->request->has('description_filter') ? $request->request->get('description_filter') : "";
            $this->designation_filter = $request->request->has('designation_filter') ? $request->request->get('designation_filter') : "";
            $this->conversationalGroup_filter = $request->request->has('conversationalGroup_filter') ? $request->request->get('conversationalGroup_filter') : "";
            $this->type_filter = $request->request->has('type_filter') ? $request->request->get('type_filter') : "";
            $this->proprietaire_filter = $request->request->has('proprietaire_filter') ? $request->request->get('proprietaire_filter') : "";
            $iDisplayLength = $request->request->has('length') ? intval($request->request->get('length')) : -1;
            $iDisplayStart = $request->request->has('start') ? intval($request->request->get('start')) : 0;
            $json = array();
            $json['items'] = array();
            $q = $request->query->has('q') ? $request->query->get('q') : "all";
            if ($q === "owner" || $q === "all") {
                $groupes = $user->getGroupesProprietaire();
                $iTotalRecords = count($groupes);
                if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
                $groupes = $this->handleResults($groupes, $iTotalRecords, $iDisplayStart, $iDisplayLength);
                $iFilteredRecords = count($groupes);
                $data = $this->get('apm_core.data_serialized')->getFormalData($groupes, array("owner_list"));
                $json['totalRecordsOwner'] = $iTotalRecords;
                $json['filteredRecordsOwner'] = $iFilteredRecords;
                $json['items']['owner'] = $data;
            }
            if ($q === "guest" || $q === "all") {
                //----- Ajout des groupes de conversation : groupes auxquels appartient l'utilisateur ---------------------
                $individu_groupes = $user->getIndividuGroupes();
                $groupesConversationnel = new ArrayCollection();;
                if (null !== $individu_groupes) {
                    foreach ($individu_groupes as $individu_groupe) {
                        /** @var Groupe_relationnel $groupe_relationnel */
                        $groupe_relationnel = $individu_groupe->getGroupeRelationnel();
                        if ($groupe_relationnel->isConversationalGroup() && $user !== $groupe_relationnel->getProprietaire()) {
                            $groupesConversationnel->add($groupe_relationnel);
                        };
                    }
                    $iTotalRecords = count($groupesConversationnel);
                    if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
                    $groupes = $this->handleResults($groupesConversationnel, $iTotalRecords, $iDisplayStart, $iDisplayLength);
                    $iFilteredRecords = count($groupes);
                    $data = $this->get('apm_core.data_serialized')->getFormalData($groupes, array("owner_list"));
                    $json['totalRecordsGuest'] = $iTotalRecords;
                    $json['filteredRecordsGuest'] = $iFilteredRecords;
                    $json['items']['guest'] = $data;
                }
                //---------------------------------------------------------------------------------------------------------
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
     * @param Collection $groupes
     * @param $iTotalRecords
     * @param $iDisplayStart
     * @param $iDisplayLength
     * @return array
     */
    private function handleResults($groupes, $iTotalRecords, $iDisplayStart, $iDisplayLength)
    {
        //filtering
        if ($groupes === null) return array();

        if ($this->code_filter != null) {
            $groupes = $groupes->filter(function ($e) {//filtrage select
                /** @var Groupe_relationnel $e */
                return $e->getCode() === $this->code_filter;
            });
        }
        if ($this->type_filter != null) {
            $groupes = $groupes->filter(function ($e) {//filtrage select
                /** @var Groupe_relationnel $e */
                return $e->getType() === $this->type_filter;
            });
        }
        if ($this->conversationalGroup_filter != null) {
            $groupes = $groupes->filter(function ($e) {//filtrage select
                /** @var Groupe_relationnel $e */
                return $e->getConversationalGroup() === boolval($this->conversationalGroup_filter);
            });
        }
        if ($this->dateCreationFrom_filter != null) {
            $groupes = $groupes->filter(function ($e) {//start date
                /** @var Groupe_relationnel $e */
                $dt1 = (new \DateTime($e->getUpdatedAt()->format('d-m-Y')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateCreationFrom_filter))->getTimestamp();
                return $dt1 - $dt2 >= 0 ? true : false; //start from the given date 'dateFrom_filter'
            });
        }
        if ($this->dateCreationTo_filter != null) {
            $groupes = $groupes->filter(function ($e) {//end date
                /** @var Groupe_relationnel $e */
                $dt = (new \DateTime($e->getUpdatedAt()->format('d-m-Y')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateCreationTo_filter))->getTimestamp();
                return $dt - $dt2 <= 0 ? true : false;// end from at the given date 'dateTo_filter'
            });
        }
        if ($this->boutique_filter != null) {
            $groupes = $groupes->filter(function ($e) {//filter with the begining of the entering word
                /** @var Groupe_relationnel $e */
                $str1 = $e->getBoutique()->getDesignation();
                $str2 = $this->boutique_filter;
                $len = strlen($str2);
                return strncasecmp($str1, $str2, $len) === 0 ? true : false;
            });
        }
        if ($this->designation_filter != null) {
            $groupes = $groupes->filter(function ($e) {//search for occurences in the text
                /** @var Groupe_relationnel $e */
                $subject = $e->getDesignation();
                $pattern = $this->designation_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }
        if ($this->description_filter != null) {
            $groupes = $groupes->filter(function ($e) {//search for occurences in the text
                /** @var Groupe_relationnel $e */
                $subject = $e->getDescription();
                $pattern = $this->description_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }
        $groupes = ($groupes !== null) ? $groupes->toArray() : [];
        //assortment: descending of date -- du plus recent au plus ancient
        usort(
            $groupes, function ($e1, $e2) {
            /**
             * @var Groupe_relationnel $e1
             * @var Groupe_relationnel $e2
             */
            $dt1 = $e1->getUpdatedAt()->getTimestamp();
            $dt2 = $e2->getUpdatedAt()->getTimestamp();
            return $dt1 <= $dt2 ? 1 : -1;
        });
        if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
        //paging; slice and preserve keys' order
        $groupes = array_slice($groupes, $iDisplayStart, $iDisplayLength, true);

        return $groupes;
    }

    /**
     * @ApiDoc(
     * resource=true,
     * resourceDescription="Operations on Groupe_relationnel.",
     * description="Create an object of type Groupe_relationnel.",
     * statusCodes={
     *         201="Returned when successful",
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
     *    "class"="APM\UserBundle\Form\Groupe_relationnelType",
     *     "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\FormTypeParser"
     *      }
     * },
     * parameters= {
     *      {"name"="imagefilex", "dataType"="integer", "required"= true, "description"="horizontal start point"},
     *      {"name"="imagefiley", "dataType"="integer", "required"= true, "description"="vertical start point"},
     *      {"name"="imagefilew", "dataType"="integer", "required"= true, "description"="width"},
     *      {"name"="imagefileh", "dataType"="integer", "required"= true, "description"="height"},
     *  },
     * views = {"default", "user" }
     * )
     * @param Request $request
     * @return View | JsonResponse
     * @Put("/new/grouperelationnel")
     * @Post("/new/grouperelationnel")
     */
    public function newAction(Request $request)
    {
        try {
            $this->createSecurity();
            /** @var Groupe_relationnel $groupe_relationnel */
            $groupe_relationnel = TradeFactory::getTradeProvider("groupe_relationnel");
            $form = $this->createForm('APM\UserBundle\Form\Groupe_relationnelType', $groupe_relationnel);
            $data = $request->request->has($form->getName()) ? $request->request->get($form->getName()) : $data[$form->getName()] = array();
            $form->submit(array_merge($data, $request->files->get($form->getName())));
            if (!$form->isValid()) {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $em = $this->getEM();
            $groupe_relationnel->setProprietaire($this->getUser());
            $em->persist($groupe_relationnel);
            $em->flush();
            $response = $request->isMethod('PUT') ? new JsonResponse(['status' => 200], Response::HTTP_OK) : $this->routeRedirectView("api_user_show_grouperelationnel", ['id' => $groupe_relationnel->getId()], Response::HTTP_CREATED);
            return $response;
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
     * @param Request $request
     * @param Groupe_relationnel $groupe_relationnel
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @get("/show-image/grouperelationnel/{id}")
     */
    public function showImageAction(Request $request, Groupe_relationnel $groupe_relationnel)
    {
        $this->listAndShowSecurity();
        $form = $this->createCrobForm($groupe_relationnel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('apm_core.crop_image')->setCropParameters(intval($_POST['x']), intval($_POST['y']), intval($_POST['w']), intval($_POST['h']), $groupe_relationnel->getImage(), $groupe_relationnel);

            return $this->redirectToRoute('apm_user_groupe-relationnel_show', array('id' => $groupe_relationnel->getId()));
        }

        return $this->render('APMUserBundle:groupe_relationnel:image.html.twig', array(
            'groupe_relationnel' => $groupe_relationnel,
            'crop_form' => $form->createView(),
            'url_image' => $this->get('apm_core.packages_maker')->getPackages()->getUrl('/', 'resolve_img'),
        ));
    }

    private
    function createCrobForm(Groupe_relationnel $groupe_relationnel)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_user_groupe-relationnel_show-image', array('id' => $groupe_relationnel->getId())))
            ->setMethod('POST')
            ->getForm();
    }

    /**
     * @ApiDoc(
     * resource=true,
     * description="Retrieve the details of an objet of type Groupe_relationnel.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * requirements = {
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="groupe_relationnel id"}
     * },
     * output={
     *   "class"="APM\UserBundle\Entity\Groupe_relationnel",
     *   "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *    },
     *     "groups"={"owner_groupeR_details", "owner_list"}
     * },
     * statusCodes={
     *     "output" = "A single Object",
     *     200="Returned when successful",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "user"}
     * )
     * @param Groupe_relationnel $groupe_relationnel
     * @return JsonResponse
     *
     * @Get("/show/grouperelationnel/{id}")
     */
    public function showAction(Groupe_relationnel $groupe_relationnel)
    {
        $this->listAndShowSecurity();
        $data = $this->get('apm_core.data_serialized')->getFormalData($groupe_relationnel, ["owner_groupeR_details", "owner_list"]);
        return new JsonResponse($data, 200);
    }

    /**
     * @ApiDoc(
     * resource=true,
     * resourceDescription="Operations on Groupe_relationnel",
     * description="Update an object of type Groupe_relationnel.",
     * statusCodes={
     *         200="Returned when successful",
     *         400="Returned when the data are not valid or an unknown error occurred",
     *         403="Returned when the user is not authorized to carry on the action",
     *         404="Returned when the entity is not found",
     * },
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"}
     * },
     * authentication= true,
     * authenticationRoles= {
     *          "ROLE_USERAVM"
     *     },
     * input={
     *    "class"="APM\UserBundle\Form\Groupe_relationnelType",
     *     "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\FormTypeParser"
     *      }
     * },
     * requirements = {
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="groupe_relationnel Id"}
     * },
     * parameters= {
     *      {"name"="imagefilex", "dataType"="integer", "required"= true, "description"="horizontal start point"},
     *      {"name"="imagefiley", "dataType"="integer", "required"= true, "description"="vertical start point"},
     *      {"name"="imagefilew", "dataType"="integer", "required"= true, "description"="width"},
     *      {"name"="imagefileh", "dataType"="integer", "required"= true, "description"="height"},
     *  },
     * views = {"default", "user" }
     * )
     * @param Request $request
     * @param Groupe_relationnel $groupe_relationnel
     * @return View | JsonResponse
     *
     * @Post("/edit/grouperelationnel/{id}")
     */
    public function editAction(Request $request, Groupe_relationnel $groupe_relationnel)
    {
        try {
            $this->editAndDeleteSecurity($groupe_relationnel);
            $form = $this->createForm('APM\UserBundle\Form\Groupe_relationnelType', $groupe_relationnel);
            $data = $request->request->has($form->getName()) ? $request->request->get($form->getName()) : $data[$form->getName()] = array();
            $form->submit(array_merge($data, $request->files->get($form->getName())), false);
            if (!$form->isValid()) {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $em = $this->getEM();
            $em->flush();
            return $this->routeRedirectView("api_user_show_grouperelationnel", ['id' => $groupe_relationnel->getId()], Response::HTTP_OK);
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
     * @param Groupe_relationnel $groupe
     *
     */
    private function editAndDeleteSecurity($groupe)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');

        /* ensure that the user is logged in
        *  and that the one is the owner
        */
        $user = $this->getUser();
        if ((!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) || ($groupe->getProprietaire() !== $user)) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------

    }


    /**
     * @ApiDoc(
     * resource=true,
     * description="Delete objet of type Groupe_relationnel.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * requirements = {
     *      {"name"="id", "dataType"="integer", "required"=true, "requirement"="\d+", "description"="Groupe_relationnel Id"}
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
     * @param Groupe_relationnel $groupe_relationnel
     * @return View | JsonResponse
     *
     * @Delete("/delete/grouperelationnel/{id}")
     */
    public function deleteAction(Request $request, Groupe_relationnel $groupe_relationnel)
    {
        try {
            $this->editAndDeleteSecurity($groupe_relationnel);
            if (!$request->request->has('exec') || $request->request->get('exec') !== 'go') {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans('impossible de supprimer', [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $em = $this->getEM();
            $em->remove($groupe_relationnel);
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
