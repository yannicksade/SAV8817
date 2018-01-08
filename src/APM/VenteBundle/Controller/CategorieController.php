<?php

namespace APM\VenteBundle\Controller;

use APM\VenteBundle\Entity\Boutique;
use APM\VenteBundle\Entity\Categorie;
use APM\VenteBundle\Factory\TradeFactory;
use APM\VenteBundle\Form\CategorieType;
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
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\Delete;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Categorie controller.
 * @RouteResource("categorie", pluralize=false)
 *
 */
class CategorieController extends FOSRestController
{
    private $code_filter;
    private $designation_filter;
    private $description_filter;
    private $livrable_filter;
    private $etat_filter;
    private $categorieCourante_filter;
    private $dateFrom_filter;
    private $dateTo_filter;
    private $publiable_filter;


    /**
     * @ApiDoc(
     * resource=true,
     * description="Retrieve list of categorie.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * filters={
     *      {"name"="designation_filter", "dataType"="string"},
     *      {"name"="description_filter", "dataType"="string"},
     *      {"name"="code_filter", "dataType"="string"},
     *      {"name"="livrable_filter", "dataType"="string"},
     *      {"name"="publiable_filter", "dataType"="boolean"},
     *      {"name"="etat_filter", "dataType"="integer"},
     *      {"name"="categorieCourante_filter", "dataType"="string"},
     *      {"name"="date_from_filter", "dataType"="dateTime", "pattern"="19-12-2017|ASC"},
     *      {"name"="date_to_filter", "dataType"="dateTime", "pattern"="19-12-2017|DESC"},
     *      {"name"="length", "dataType"="integer", "requirement"="\d+"},
     *      {"name"="start", "dataType"="integer", "requirement"="\d+"},
     *  },
     * output={
     *   "class"="APM\VenteBundle\Entity\Categorie",
     *   "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *    },
     *     "groups"={"owner_list"}
     * },
     * requirements={
     *       {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="boutique Id"}
     *  },
     *
     * statusCodes={
     *     "output" = "A single or a collection of categorie",
     *     200="Returned when successful",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "vente"}
     * )
     * @param Request $request
     * @param Boutique $boutique
     * @return JsonResponse
     *
     * @Get("/cget/categories/boutique/{id}", name="s")
     */
    public function getAction(Request $request, Boutique $boutique)
    {
        try {
            $this->listAndShowSecurity($boutique);
            $categories = $boutique->getCategories();
            $this->code_filter = $request->query->has('code_filter') ? $request->query->get('code_filter') : "";
            $this->designation_filter = $request->query->has('designation_filter') ? $request->query->get('designation_filter') : "";
            $this->description_filter = $request->query->has('description_filter') ? $request->query->get('description_filter') : "";
            $this->livrable_filter = $request->query->has('livrable_filter') ? $request->query->get('livrable_filter') : "";
            $this->publiable_filter = $request->query->has('publiable_filter') ? $request->query->get('publiable_filter') : "";
            $this->etat_filter = $request->query->has('etat_filter') ? $request->query->get('etat_filter') : "";
            $this->categorieCourante_filter = $request->query->has('categorieCourante_filter') ? $request->query->get('categorieCourante_filter') : "";
            $this->dateFrom_filter = $request->query->has('date_from_filter') ? $request->query->get('date_from_filter') : "";
            $this->dateTo_filter = $request->query->has('date_to_filter') ? $request->query->get('date_to_filter') : "";
            $iDisplayLength = $request->query->has('length') ? $request->query->get('length') : -1;
            $iDisplayStart = $request->query->has('start') ? $request->query->get('start') : 0;
            $json = array();
            $selectedGroup = array("owner_list");
            $iTotalRecords = count($categories);
            if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
            $categories = $this->handleResults($categories, $iTotalRecords, $iDisplayStart, $iDisplayLength);
            $iFilteredRecords = count($categories);
            $data = $this->get('apm_core.data_serialized')->getFormalData($categories, $selectedGroup);
            $json['totalRecords'] = $iTotalRecords;
            $json['filteredRecords'] = $iFilteredRecords;
            $json['items'] = $data;
            return new JsonResponse($json, 200);
        } catch (AccessDeniedException $ads) {
            return new JsonResponse([
                "status" => 403,
                "message" => $this->get('translator')->trans("Accès refusé", [], 'FOSUserBundle')
            ], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * @param Boutique $boutique
     */
    private function listAndShowSecurity($boutique = null)
    {
        //-----------------------------------security-------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted(['ROLE_BOUTIQUE', 'ROLE_USERAVM'], null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw $this->createAccessDeniedException();
        }
        if ($boutique) {
            $user = $this->getUser();
            $proprietaire = $boutique->getProprietaire();
            $gerant = $boutique->getGerant();
            if ($user !== $gerant && $user !== $proprietaire) {
                throw $this->createAccessDeniedException();
            }
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @param Collection $categories
     * @param $iTotalRecords
     * @param $iDisplayStart
     * @param $iDisplayLength
     * @return array
     */
    private function handleResults($categories, $iTotalRecords, $iDisplayStart, $iDisplayLength)
    {
        //filtering
        if ($categories === null) return array();

        if ($this->code_filter != null) {
            $categories = $categories->filter(function ($e) {//filtrage select
                /** @var Categorie $e */
                return $e->getCode() === $this->code_filter;
            });
        }
        if ($this->livrable_filter != null) {
            $categories = $categories->filter(function ($e) {//filtrage select
                /** @var Categorie $e */
                return $e->getLivrable() === boolval($this->livrable_filter);
            });
        }
        if ($this->publiable_filter != null) {
            $categories = $categories->filter(function ($e) {//filtrage select
                /** @var Categorie $e */
                return $e->getCode() === boolval($this->publiable_filter);
            });
        }
        if ($this->etat_filter != null) {
            $categories = $categories->filter(function ($e) {//filtrage select
                /** @var Categorie $e */
                return $e->getEtat() === $this->etat_filter;
            });
        }
        if ($this->dateFrom_filter != null) {
            $categories = $categories->filter(function ($e) {//start date
                /** @var Categorie $e */
                $dt1 = (new \DateTime($e->getDateCreation()->format('d-m-Y')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateFrom_filter))->getTimestamp();
                return $dt1 - $dt2 >= 0 ? true : false; //start from the given date 'dateFrom_filter'
            });
        }
        if ($this->dateTo_filter != null) {
            $categories = $categories->filter(function ($e) {//end date
                /** @var Categorie $e */
                $dt = (new \DateTime($e->getDateCreation()->format('d-m-Y')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateTo_filter))->getTimestamp();
                return $dt - $dt2 <= 0 ? true : false;// end from at the given date 'dateTo_filter'
            });
        }
        if ($this->categorieCourante_filter != null) {
            $categories = $categories->filter(function ($e) {//filter with the begining of the entering word
                /** @var Categorie $e */
                $str1 = $e->getBoutique()->getDesignation();
                $str2 = $this->categorieCourante_filter;
                $len = strlen($str2);
                return strncasecmp($str1, $str2, $len) === 0 ? true : false;
            });
        }
        if ($this->designation_filter != null) {
            $categories = $categories->filter(function ($e) {//search for occurences in the text
                /** @var Categorie $e */
                $subject = $e->getDesignation();
                $pattern = $this->designation_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }
        if ($this->description_filter != null) {
            $categories = $categories->filter(function ($e) {//search for occurences in the text
                /** @var Categorie $e */
                $subject = $e->getDescription();
                $pattern = $this->description_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }
        $categories = ($categories !== null) ? $categories->toArray() : [];
        //assortment: descending of date -- du plus recent au plus ancient
        usort(
            $categories, function ($e1, $e2) {
            /**
             * @var Categorie $e1
             * @var Categorie $e2
             */
            $dt1 = $e1->getDateCreation()->getTimestamp();
            $dt2 = $e2->getDateCreation()->getTimestamp();
            return $dt1 <= $dt2 ? 1 : -1;
        });
        if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
        //paging; slice and preserve keys' order
        $categories = array_slice($categories, $iDisplayStart, $iDisplayLength, true);

        return $categories;
    }

    /**
     * @ApiDoc(
     * resource=true,
     * resourceDescription="Operations on Categorie.",
     * description="Create an object of type Categorie.",
     * statusCodes={
     *         201="Returned when successful",
     *         400="Returned when the data are not valid or an unknown error occurred",
     *         403="Returned when the user is not authorized to carry on the action",
     *         404="Returned when the entity is not found",
     * },
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="boutique Id"}
     *  },
     * headers={
     *      { "name"="Authorization",  "required"=true, "description"="Authorization token"}
     * },
     * input={
     *    "class"="APM\VenteBundle\Entity\Categorie",
     *     "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\ValidationParser"
     *      },
     *    "name" = "Categorie",
     * },
     *      views = {"default", "vente" }
     * )
     * @param Request $request
     * @param Boutique $boutique
     * @Post("/new/categorie/boutique/{id}", name="_boutique")
     * @return JsonResponse | View
     */
    public function newAction(Request $request, Boutique $boutique)
    {
        try {
            $this->createSecurity($boutique);
            /** @var Categorie $categorie */
            $categorie = TradeFactory::getTradeProvider('categorie');
            $form = $this->createForm(CategorieType::class, $categorie);
            $form->submit($request->request->all());
            if (!$form->isValid()) {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $this->createSecurity($boutique, $categorie->getCategorieCourante());
            $categorie->setBoutique($boutique);
            $em = $this->getEM();
            $em->persist($categorie);
            $em->flush();
            return $this->routeRedirectView("api_vente_show_categorie", ['id' => $categorie->getId()], Response::HTTP_CREATED);
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
     * @param Categorie $categorieCourante
     */
    private function createSecurity($boutique = null, $categorieCourante = null)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_BOUTIQUE', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        //Interdire tout utilisateur si ce n'est pas le gerant ou le proprietaire
        if ($boutique) {
            $user = $this->getUser();
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
            if ($user !== $gerant && $user !== $proprietaire) {
                throw $this->createAccessDeniedException();
            }
            if ($categorieCourante) {
                $currentBoutique = $categorieCourante->getBoutique();
                if ($currentBoutique !== $boutique) {
                    throw $this->createAccessDeniedException();
                }
            }
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
     * description="Retrieve the details of an objet of type Categorie.",
     * headers={
     *      { "name"="Authorization", "required"=true, "description"="Authorization token"},
     * },
     * requirements = {
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="categorie id"}
     * },
     * output={
     *   "class"="APM\VenteBundle\Entity\Categorie",
     *   "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *    },
     *     "groups"={"owner_categorie_details", "owner_list"}
     * },
     * statusCodes={
     *     "output" = "A single Object",
     *     200="Returned when successful",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "vente"}
     * )
     * @param Categorie $categorie
     * @return JsonResponse
     *
     * @Get("/show/categorie/{id}")
     */
    public function showAction(Categorie $categorie)
    {
        $this->listAndShowSecurity();
        $data = $this->get('apm_core.data_serialized')->getFormalData($categorie, ["owner_categorie_details", "owner_list"]);
        return new JsonResponse($data, 200);
    }

    /**
     * @ApiDoc(
     * resource=true,
     * resourceDescription="Operations on Categorie",
     * description="Update an object of type Categorie.",
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
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="categorie Id"}
     * },
     * input={
     *    "class"="APM\VenteBundle\Entity\Categorie",
     *     "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\ValidationParser"
     *      },
     *    "name" = "Categorie",
     * },
     *
     * views = {"default", "vente" }
     * )
     * @param Request $request
     * @param Categorie $categorie
     * @return View |JsonResponse
     *
     * @Put("/edit/categorie/{id}")
     */
    public function editAction(Request $request, Categorie $categorie)
    {
        try {
            $this->editAndDeleteSecurity($categorie);
            $form = $this->createForm('APM\VenteBundle\Form\CategorieType', $categorie);
            $form->submit($request->request->all(), false);
            if (!$form->isValid()) {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $em = $this->getEM();
            $em->flush();
            return $this->routeRedirectView("api_vente_show_categorie", ['id' => $categorie->getId()], Response::HTTP_OK);
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
     * @param Categorie $categorie
     */
    private function editAndDeleteSecurity($categorie)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_BOUTIQUE', null, 'Unable to access this page!');
        $user = $this->getUser();
        $boutique = $categorie->getBoutique();
        $gerant = $boutique->getGerant();
        $proprietaire = $boutique->getProprietaire();
        if ((!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) || ($gerant !== $user && $user !== $proprietaire)) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------

    }

    /**
     * @ApiDoc(
     * resource=true,
     * description="Delete objet of type Categorie.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * requirements = {
     *      {"name"="id", "dataType"="integer", "required"=true, "requirement"="\d+", "description"="categorie Id"}
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
     * @param Categorie $categorie
     * @return View | JsonResponse
     *
     * @Delete("/delete/categorie/{id}")
     */
    public function deleteAction(Request $request, Categorie $categorie)
    {
        try {
            $this->editAndDeleteSecurity($categorie);
            if (!$request->request->has('exec') || $request->request->get('exec') !== 'go') {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans('impossible de supprimer', [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $boutique = $categorie->getBoutique();
            $em = $this->getEM();
            $em->remove($categorie);
            $em->flush();
            return $this->routeRedirectView("api_vente_get_categories", [$boutique->getId()], Response::HTTP_OK);
        } catch (ConstraintViolationException $cve) {
            return new JsonResponse([
                "status" => 400,
                "message" => $this->get('translator')->trans("impossible d'enregistrer, vérifiez vos données", [], 'FOSUserBundle')
            ], Response::HTTP_FAILED_DEPENDENCY);
        } catch (AccessDeniedException $ads) {
            return new JsonResponse([
                    "status" => 403,
                    "message" => $this->get('translator')->trans("Access denied", [], 'FOSUserBundle'),
                ]
                , Response::HTTP_FORBIDDEN
            );
        }
    }

}
