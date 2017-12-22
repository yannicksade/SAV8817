<?php

namespace APM\AchatBundle\Controller;

use APM\AchatBundle\Entity\Specification_achat;
use APM\AchatBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Offre;
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
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Patch;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Specification_achat controller.
 * @RouteResource("specification", pluralize=false)
 */
class Specification_achatController extends FOSRestController
{
    private $demandeRabais_filter;
    private $livraison_filter;
    private $code_filter;
    private $dateLivraisonFrom_filter;
    private $dateLivraisonTo_filter;
    private $avis_filter;
    private $echantillon_filter;
    private $offre_filter;
    private $utilisateur_filter;
    private $dateFrom_filter;
    private $dateTo_filter;

    /**
     * @ApiDoc(
     * resource=true,
     * description="Retrieve list of specification.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * filters={
     *      {"name"="offre_filter", "dataType"="string"},
     *      {"name"="code_filter", "dataType"="string"},
     *      {"name"="dateFrom_filter", "dataType"="dateTime", "pattern"="19-12-2017|ASC"},
     *      {"name"="dateTo_filter", "dataType"="dateTime", "pattern"="19-12-2017|DESC"},
     *      {"name"="dateLivraisonFrom_filter", "dataType"="dateTime", "pattern"="19-12-2017|ASC"},
     *      {"name"="dateLivraisonTo_filter", "dataType"="dateTime", "pattern"="19-12-2017|DESC"},
     *      {"name"="livraison_filter", "dataType"="boolean"},
     *      {"name"="avis_filter", "dataType"="string"},
     *      {"name"="demandeRabais_filter", "dataType"="boolean"},
     *      {"name"="echantillon_filter", "dataType"="boolean"},
     *      {"name"="utilisateur_filter", "dataType"="string", "pattern"="yannick|USERNAME"},
     *      {"name"="length_filter", "dataType"="integer", "requirement"="\d+"},
     *      {"name"="start_filter", "dataType"="integer", "requirement"="\d+"},
     *  },
     *
     * output={
     *   "class"="APM\AchatBundle\Entity\Specification_achat",
     *   "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *    },
     *     "groups"={"owner_list"}
     * },
     * statusCodes={
     *     "output" = "A single or a collection of Specification",
     *     200="Returned when successful",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "achat"}
     * )
     * @param Offre $offre
     * @return  JsonResponse
     *
     * @Get("/cget/specifications", name="s")
     * @Get("/cget/specifications/offre/{id}", name="s_offre", requirements={"id"="offre_id"})
     */
    public function getAction(Offre $offre = null)
    {
        try {
            $this->listAndShowSecurity($offre);
            if (null !== $offre) {
                $specification_achats = $offre->getSpecifications();
            } else {
                /** @var Utilisateur_avm $user */
                $user = $this->getUser();
                $specification_achats = $user->getSpecifications();
            }

            $this->demandeRabais_filter = $request->query->has('demandeRabais_filter') ? $request->query->get('demandeRabais_filter') : "";
            $this->code_filter = $request->query->has('code_filter') ? $request->query->get('code_filter') : "";
            $this->livraison_filter = $request->query->has('livraison_filter') ? $request->query->get('livraison_filter') : "";
            $this->dateLivraisonFrom_filter = $request->query->has('dateLivraisonFrom_filter') ? $request->query->get('dateLivraisonFrom_filter') : "";
            $this->dateLivraisonTo_filter = $request->query->has('dateLivraisonTo_filter') ? $request->query->get('dateLivraisonTo_filter') : "";
            $this->dateFrom_filter = $request->query->has('dateFrom_filter') ? $request->query->get('dateFrom_filter') : "";
            $this->dateTo_filter = $request->query->has('dateTo_filter') ? $request->query->get('dateTo_filter') : "";
            $this->avis_filter = $request->query->has('avis_filter') ? $request->query->get('avis_filter') : "";
            $this->echantillon_filter = $request->query->has('echantillon_filter') ? $request->query->get('echantillon_filter') : "";
            $this->offre_filter = $request->query->has('offre_filter') ? $request->query->get('offre_filter') : "";
            $this->utilisateur_filter = $request->query->has('utilisateur_filter') ? $request->query->get('utilisateur_filter') : "";

            $iDisplayLength = $request->query->has('length') ? $request->query->get('length') : -1;
            $iDisplayStart = $request->query->has('start') ? intval($request->query->get('start')) : 0;
            $json = array();
            $iTotalRecords = count($specification_achats);
            if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
            $specification_achats = $this->handleResults($specification_achats, $iTotalRecords, $iDisplayStart, $iDisplayLength);
            $iFilteredRecords = count($specification_achats);
            $data = $this->get('apm_core.data_serialized')->getFormalData($specification_achats, array("owner_list"));
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
     * @param Offre |null $offre
     */
    private function listAndShowSecurity($offre = null)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        if ($offre) {
            $user = $this->getUser();
            $vendeur = $offre->getVendeur();
            $boutique = $offre->getBoutique();
            $gerant = null;
            $proprietaire = null;
            if ($boutique) {
                $gerant = $boutique->getGerant();
                $proprietaire = $boutique->getProprietaire();
            }
            if ($user !== $vendeur && $user !== $gerant && $user !== $proprietaire) {
                throw $this->createAccessDeniedException();
            }

        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @param Collection $specifications
     * @param $iTotalRecords
     * @param $iDisplayStart
     * @param $iDisplayLength
     * @return array
     */
    private function handleResults($specifications, $iTotalRecords, $iDisplayStart, $iDisplayLength)
    {
        //filtering
        if ($specifications === null) return array();

        if ($this->code_filter != null) {
            $specifications = $specifications->filter(function ($e) {//filtrage select
                /** @var Specification_achat $e */
                return $e->getCode() === $this->code_filter;
            });
        }
        if ($this->livraison_filter != null) {
            $specifications = $specifications->filter(function ($e) {//filtrage select
                /** @var Specification_achat $e */
                return $e->getLivraison() === boolval($this->livraison_filter);
            });
        }
        if ($this->demandeRabais_filter != null) {
            $specifications = $specifications->filter(function ($e) {//filtrage select
                /** @var Specification_achat $e */
                return $e->getDemandeRabais() === boolval($this->demandeRabais_filter);
            });
        }
        if ($this->echantillon_filter != null) {
            $specifications = $specifications->filter(function ($e) {//filtrage select
                /** @var Specification_achat $e */
                return $e->getEchantillon() === boolval($this->echantillon_filter);
            });
        }
        if ($this->utilisateur_filter != null) {
            $specifications = $specifications->filter(function ($e) {//filter with the begining of the entering word
                /** @var Specification_achat $e */
                $str1 = $e->getUtilisateur()->getCode();
                $str2 = $this->utilisateur_filter;
                return strcasecmp($str1, $str2) === 0 ? true : false;
            });
        }
        if ($this->offre_filter != null) {
            $specifications = $specifications->filter(function ($e) {//filter with the begining of the entering word
                /** @var Specification_achat $e */
                $str1 = $e->getOffre()->getCode();
                $str2 = $this->offre_filter;
                return strcasecmp($str1, $str2) === 0 ? true : false;
            });
        }
        if ($this->avis_filter != null) {
            $specifications = $specifications->filter(function ($e) {//search for occurences in the text
                /** @var Specification_achat $e */
                $subject = $e->getAvis();
                $pattern = $this->avis_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }
        if ($this->dateLivraisonFrom_filter != null) {
            $specifications = $specifications->filter(function ($e) {//start date
                /** @var Specification_achat $e */
                $dt1 = (new \DateTime($e->getDateLivraisonSouhaite()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateLivraisonFrom_filter))->getTimestamp();
                return $dt1 - $dt2 >= 0 ? true : false; //start from the given date 'dateFrom_filter'
            });
        }
        if ($this->dateLivraisonTo_filter != null) {
            $specifications = $specifications->filter(function ($e) {//end date
                /** @var Specification_achat $e */
                $dt = (new \DateTime($e->getDateCreation()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateLivraisonTo_filter))->getTimestamp();
                return $dt - $dt2 <= 0 ? true : false;// end from at the given date 'dateTo_filter'
            });
        }
        if ($this->dateFrom_filter != null) {
            $specifications = $specifications->filter(function ($e) {//start date
                /** @var Specification_achat $e */
                $dt1 = (new \DateTime($e->getDateCreation()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateFrom_filter))->getTimestamp();
                return $dt1 - $dt2 >= 0 ? true : false; //start from the given date 'dateFrom_filter'
            });
        }
        if ($this->dateTo_filter != null) {
            $specifications = $specifications->filter(function ($e) {//end date
                /** @var Specification_achat $e */
                $dt = (new \DateTime($e->getDateCreation()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateTo_filter))->getTimestamp();
                return $dt - $dt2 <= 0 ? true : false;// end from at the given date 'dateTo_filter'
            });
        }

        $specifications = ($specifications !== null) ? $specifications->toArray() : [];
        //assortment: descending of date -- du plus recent au plus ancient
        usort(
            $specifications, function ($e1, $e2) {
            /**
             * @var Specification_achat $e1
             * @var Specification_achat $e2
             */
            $dt1 = $e1->getDateCreation()->getTimestamp();
            $dt2 = $e2->getDateCreation()->getTimestamp();
            return $dt1 <= $dt2 ? 1 : -1;
        });
        if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
        //paging; slice and preserve keys' order
        $specifications = array_slice($specifications, $iDisplayStart, $iDisplayLength, true);

        return $specifications;
    }

    /**
     * @ApiDoc(
     * resource=true,
     * resourceDescription="Operations on Specification achat.",
     * description="Create an object of type Specification_achat.",
     * statusCodes={
     *         201="Returned when successful",
     *         400="Returned when the data are not valid or an unknown error occurred",
     *         403="Returned when the user is not authorized to carry on the action",
     *         404="Returned when the entity is not found",
     * },
     * headers={
     *      { "name"="Authorization", "required"=true, "description"="Authorization token"}
     * },
     * requirements={
    {"name"="id", "requirement"="\d+", "dataType"="integer", "description"="offre_id"}
     * },
     * input={
     *    "class"="APM\AchatBundle\Entity\Specification_achat",
     *     "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\ValidationParser"
     *      },
     *    "name" = "Specification_achat",
     * },
     *  views = {"default", "achat" }
     * )
     * @param Offre $offre
     * @return View | JsonResponse
     *
     * @Post("/new/specification/offre/{id}")
     */
    public function newAction(Offre $offre)
    {
        try {
            $this->createSecurity();
            /** @var Specification_achat $specification_achat */
            $specification_achat = TradeFactory::getTradeProvider("specification_achat");
            $form = $this->createForm('APM\AchatBundle\Form\Specification_achatType', $specification_achat);
            if (!$form->isValid()) {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $specification_achat->setUtilisateur($this->getUser());
            $specification_achat->setOffre($offre);
            $em = $this->getDoctrine()->getManager();
            $em->persist($specification_achat);
            $em->flush();
            return $this->routeRedirectView("api_achat_show_specification", ['id' => $specification_achat->getId()], Response::HTTP_CREATED);

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
     * description="Retrieve the details of an objet of type Specification_achat.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * requirements = {
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="specification_id"}
     * },
     * output={
     *   "class"="APM\AchatBundle\Entity\Specification_achat",
     *   "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *    },
     *     "groups"={"owner_spA_details", "owner_list"}
     * },
     * statusCodes={
     *     "output" = "A single Object",
     *     200="Returned when successful",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "achat"}
     * )
     * @param Specification_achat $specification
     * @return \Symfony\Component\HttpFoundation\Response| JsonResponse
     *
     * @Get("/show/specification/{id}")
     */
    public function showAction(Specification_achat $specification)
    {
        $this->listAndShowSecurity();
        $data = $this->get('apm_core.data_serialized')->getFormalData($specification, ["owner_spA_details", "owner_list"]);
        return new JsonResponse($data, 200);
    }

    /**
     * @ApiDoc(
     * resource=true,
     * resourceDescription="Operations on specification achat",
     * description="Update an object of type specification_achat.",
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
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="specification Id"}
     * },
     * input={
     *    "class"="APM\AchatBundle\Entity\Specification_achat",
     *     "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\ValidationParser"
     *      },
     *    "name" = "Specification_achat",
     * },
     *     views={"default","achat"}
     *)
     * @param Request $request
     * @param Specification_achat $specification_achat
     * @return JsonResponse | View
     *
     * @Put("/edit/specification/{id}")
     */
    public function editAction(Request $request, Specification_achat $specification_achat)
    {
        try {
            $this->editAndDeleteSecurity($specification_achat);
            $form = $this->createForm('APM\AchatBundle\Form\Specification_achatType', $specification_achat);
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
            return $this->routeRedirectView("api_achat_show_specification ", ["id" => $specification_achat->getId()], Response::HTTP_OK);
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
     * @param Specification_achat $specification_achat
     */
    private function editAndDeleteSecurity($specification_achat)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');

        /* ensure that the user is logged in  # granted even through remembering cookies
        */
        $user = $this->getUser();
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED') || $user !== $specification_achat->getUtilisateur()) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @ApiDoc(
     * resource=true,
     * description="Delete objet of type Specification_achat.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * requirements = {
     *      {"name"="id", "dataType"="integer", "required"=true, "requirement"="\d+", "description"="specification Id"}
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
     * @param Specification_achat $specification_achat
     * @return View | JsonResponse
     * @Delete("/delete/specification/{id}")
     */
    public function deleteAction(Request $request, Specification_achat $specification_achat)
    {
        try {
            $this->editAndDeleteSecurity($specification_achat);
            if (!$request->request->has('exec') || $request->request->get('exec') !== 'go') {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans('impossible de supprimer', [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $em = $this->getDoctrine()->getManager();
            $em->remove($specification_achat);
            $em->flush();

            return $this->routeRedirectView("api_achat_get_specifications", [], Response::HTTP_OK);

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
