<?php

namespace APM\VenteBundle\Controller;

use APM\UserBundle\Entity\Groupe_relationnel;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Offre;
use APM\VenteBundle\Entity\Rabais_offre;
use APM\VenteBundle\Factory\TradeFactory;
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
 * Rabais_offre controller.
 * @RouteResource("rabais", pluralize=false)
 */
class Rabais_offreController extends FOSRestController
{
    private $beneficiaire_filter;
    private $code_filter;
    private $nombreDefois_filter;
    private $quantite_filter;
    private $groupe_filter;
    private $vendeur_filter;
    private $offre_filter;
    private $prixUpdateMax_filter;
    private $prixUpdateMin_filter;
    private $dateLimiteTo_filter;
    private $dateLimiteFrom_filter;


    /**
     * @ApiDoc(
     * resource=true,
     * description="Retrieve list of rabais offre.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * filters={
     *      {"name"="beneficiaire_filter", "dataType"="string"},
     *      {"name"="dateLimiteFrom_filter", "dataType"="dateTime", "pattern"="19-12-2017|ASC"},
     *      {"name"="dateLimiteTo_filter", "dataType"="dateTime", "pattern"="19-12-2017|DESC"},
     *      {"name"="description_filter", "dataType"="string"},
     *      {"name"="code_filter", "dataType"="string"},
     *      {"name"="nombreDefois_filter", "dataType"="integer"},
     *      {"name"="prixUpdateMin_filter", "dataType"="integer"},
     *      {"name"="prixUpdateMax_filter", "dataType"="integer"},
     *      {"name"="quantite_filter", "dataType"="integer"},
     *      {"name"="vendeur_filter", "dataType"="string"},
     *      {"name"="offre_filter", "dataType"="string"},
     *      {"name"="groupe_filter", "dataType"="string"},
     *      {"name"="length_filter", "dataType"="integer", "requirement"="\d+"},
     *      {"name"="start_filter", "dataType"="integer", "requirement"="\d+"},
     *  },
     * output={
     *   "class"="APM\VenteBundle\Entity\Boutique",
     *   "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *    },
     *     "groups"={"owner_list"}
     * },
     *  parameters= {
     *      {"name"="q", "required"="false", "dataType"="string", "requirement"="\D+", "description"="query request == product | received | anonymous ==", "format"= "?q=product"}
     *  },
     * statusCodes={
     *     "output" = "A single or a collection of rabais",
     *     200="Returned when successful",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "vente"}
     * )
     *
     * @param Request $request
     * @param Offre $offre
     * @return JsonResponse
     *
     * @Get("/cget/rabaisoffres/utilisateur")
     * @Get("/cget/rabaisoffres/offre/{id}", name="_offre")
     */
    public function getAction(Request $request, Offre $offre = null)
    {
        try {
            $this->listAndShowSecurity($offre);
            /** @var Utilisateur_avm $user */
            $user = $this->getUser();
            $this->beneficiaire_filter = $request->query->has('beneficiaire_filter') ? $request->query->get('beneficiaire_filter') : "";
            $this->code_filter = $request->query->has('code_filter') ? $request->query->get('code_filter') : "";
            $this->dateLimiteFrom_filter = $request->query->has('dateLimiteFrom_filter') ? $request->query->get('dateLimiteFrom_filter') : "";
            $this->dateLimiteTo_filter = $request->query->has('dateLimiteTo_filter') ? $request->query->get('dateLimiteTo_filter') : "";
            $this->nombreDefois_filter = $request->query->has('nombreDefois_filter') ? $request->query->get('nombreDefois_filter') : "";
            $this->prixUpdateMin_filter = $request->query->has('prixUpdateMin_filter') ? $request->query->get('prixUpdateMin_filter') : "";
            $this->prixUpdateMax_filter = $request->query->has('prixUpdateMax_filter') ? $request->query->get('prixUpdateMax_filter') : "";
            $this->quantite_filter = $request->query->has('quantite_filter') ? $request->query->get('quantite_filter') : "";
            $this->vendeur_filter = $request->query->has('vendeur_filter') ? $request->query->get('vendeur_filter') : "";
            $this->offre_filter = $request->query->has('offre_filter') ? $request->query->get('offre_filter') : "";
            $this->groupe_filter = $request->query->has('groupe_filter') ? $request->query->get('groupe_filter') : "";
            $iDisplayLength = $request->query->has('length') ? $request->query->get('length') : -1;
            $iDisplayStart = $request->query->has('start') ? intval($request->query->get('start')) : 0;
            $json = array();
            $rabais_offres = null;
            $q = $request->query->has('q') ? $request->query->get('q') : 'all';
            $json['items'] = array();
            if ($q === "product" || $q === "all") {
                if (null !== $offre) $rabais_offres = $offre->getRabais();
                if (null !== $rabais_offres) {
                    $iTotalRecords = count($rabais_offres);
                    if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
                    $rabais_offres = $this->handleResults($rabais_offres, $iTotalRecords, $iDisplayStart, $iDisplayLength);
                    $iFilteredRecords = count($rabais_offres);
                    $data = $this->get('apm_core.data_serialized')->getFormalData($rabais_offres, array("owner_list"));
                    $json['totalRecordsFromProduct'] = $iTotalRecords;
                    $json['filteredRecordsFromProduct'] = $iFilteredRecords;
                    $json['items'] = $data;
                }
            }

            if ($q === "anonymous" || $q === "all") {
                $rabais_recus = $user->getRabaisRecus();
                if (null !== $rabais_recus) {
                    $iTotalRecords = count($rabais_recus);
                    if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
                    $rabais_recus = $this->handleResults($rabais_recus, $iTotalRecords, $iDisplayStart, $iDisplayLength);
                    $iFilteredRecords = count($rabais_offres);
                    $data = $this->get('apm_core.data_serialized')->getFormalData($rabais_recus, array("owner_list"));
                    $json['totalRecordsSent'] = $iTotalRecords;
                    $json['filteredRecordsSent'] = $iFilteredRecords;
                    $json['items'] = $data;
                }
            }

            if ($q === "received" || $q === "all") {
                $rabais_accordes = $user->getRabaisAccordes();
                if (null !== $rabais_accordes) {
                    $iTotalRecords = count($rabais_accordes);
                    if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
                    $rabais_accordes = $this->handleResults($rabais_accordes, $iTotalRecords, $iDisplayStart, $iDisplayLength);
                    $iFilteredRecords = count($rabais_accordes);
                    $data = $this->get('apm_core.data_serialized')->getFormalData($rabais_accordes, array("others_list"));
                    $json['totalRecordsReceived'] = $iTotalRecords;
                    $json['filteredRecordsReceived'] = $iFilteredRecords;
                    $json['items'] = $data;
                }
            }
            return new JsonResponse($json, 200);
        } catch (AccessDeniedException $ads) {
            return new JsonResponse([
                "status" => 403,
                "message" => $this->get('translator')->trans("Accès refusé", [], 'FOSUserBundle')
            ], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * @param Rabais_offre|null $rabais
     * @param Offre $offre
     */
    private function listAndShowSecurity($offre = null, $rabais = null)
    {
        //-----------------------------------security-------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw $this->createAccessDeniedException();
        }

        if ($offre) { /// N'autorise que le vendeur, le gerant ou le proprietaire ou le bénéficiare à pouvoir afficher des rabais sur l'offre
            $user = $this->getUser();
            $boutique = $offre->getBoutique();
            $gerant = null;
            $proprietaire = null;
            $beneficiaire = null;
            if ($boutique) {
                $gerant = $boutique->getGerant();
                $proprietaire = $boutique->getProprietaire();
            }
            $vendeur = $offre->getVendeur();
            if ($rabais) {//beneficiare
                $beneficiaire = $rabais->getBeneficiaireRabais();
            }
            if ($user !== $gerant && $user !== $proprietaire && $user !== $vendeur && $user !== $beneficiaire) {
                throw $this->createAccessDeniedException();
            }
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @param Collection $rabais
     * @param $iTotalRecords
     * @param $iDisplayStart
     * @param $iDisplayLength
     * @return array
     */
    private function handleResults($rabais, $iTotalRecords, $iDisplayStart, $iDisplayLength)
    {
        //filtering
        if ($rabais === null) return array();

        if ($this->code_filter != null) {
            $rabais = $rabais->filter(function ($e) {//filtrage select
                /** @var Rabais_offre $e */
                return $e->getCode() === $this->code_filter;
            });
        }
        if ($this->quantite_filter != null) {
            $rabais = $rabais->filter(function ($e) {//filtrage select
                /** @var Rabais_offre $e */
                return $e->getQuantiteMin() <= $this->quantite_filter;
            });
        }
        if ($this->groupe_filter != null) {
            $rabais = $rabais->filter(function ($e) {//filtrage select
                /** @var Rabais_offre $e */
                return $e->getGroupe()->getCode() === $this->groupe_filter;
            });
        }
        if ($this->prixUpdateMin_filter != null) {
            $rabais = $rabais->filter(function ($e) {//filtrage select
                /** @var Rabais_offre $e */
                return intval($e->getPrixUpdate()) >= intval($this->prixUpdateMin_filter);
            });
        }
        if ($this->prixUpdateMax_filter != null) {
            $rabais = $rabais->filter(function ($e) {//filtrage select
                /** @var Rabais_offre $e */
                return intval($e->getPrixUpdate()) <= intval($this->prixUpdateMax_filter);
            });
        }
        if ($this->dateLimiteFrom_filter != null) {
            $rabais = $rabais->filter(function ($e) {//start date
                /** @var Rabais_offre $e */
                $dt1 = (new \DateTime($e->getDateLimite()->format('d-m-Y')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateLimiteFrom_filter))->getTimestamp();
                return $dt1 - $dt2 >= 0 ? true : false; //start from the given date 'dateFrom_filter'
            });
        }
        if ($this->dateLimiteTo_filter != null) {
            $rabais = $rabais->filter(function ($e) {//end date
                /** @var Rabais_offre $e */
                $dt = (new \DateTime($e->getDateLimite()->format('d-m-Y')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateLimiteTo_filter))->getTimestamp();
                return $dt - $dt2 <= 0 ? true : false;// end from at the given date 'dateTo_filter'
            });
        }
        if ($this->offre_filter != null) {
            $rabais = $rabais->filter(function ($e) {//filter with the begining of the entering word
                /** @var Rabais_offre $e */
                $str1 = $e->getOffre()->getDesignation();
                $str2 = $this->offre_filter;
                $len = strlen($str2);
                return strncasecmp($str1, $str2, $len) === 0 ? true : false;
            });
        }

        if ($this->beneficiaire_filter != null) {
            $rabais = $rabais->filter(function ($e) {//search for occurences in the text
                /** @var Rabais_offre $e */
                $subject = $e->getBeneficiaireRabais()->getUsername();
                $pattern = $this->beneficiaire_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }
        if ($this->vendeur_filter != null) {
            $rabais = $rabais->filter(function ($e) {//search for occurences in the text
                /** @var Rabais_offre $e */
                $subject = $e->getVendeur()->getUsername();
                $pattern = $this->vendeur_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }

        $rabais = ($rabais !== null) ? $rabais->toArray() : [];
        //assortment: descending of date -- du plus recent au plus ancient
        usort(
            $rabais, function ($e1, $e2) {
            /**
             * @var Rabais_offre $e1
             * @var Rabais_offre $e2
             */
            $dt1 = $e1->getDateLimite()->getTimestamp();
            $dt2 = $e2->getDateLimite()->getTimestamp();
            return $dt1 <= $dt2 ? 1 : -1;
        });
        if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
        //paging; slice and preserve keys' order
        $rabais = array_slice($rabais, $iDisplayStart, $iDisplayLength, true);

        return $rabais;
    }

    /**
     * @ApiDoc(
     * resource=true,
     * resourceDescription="Operations on Rabais_offre.",
     * description="Create an object of type Rabais_offre.",
     * statusCodes={
     *         201="Returned when successful",
     *         400="Returned when the data are not valid or an unknown error occurred",
     *         403="Returned when the user is not authorized to carry on the action",
     *         404="Returned when the entity is not found",
     * },
     * headers={
     *      { "name"="Authorization",  "required"=true, "description"="Authorization token"}
     * },
     *  requirements={
     *      {"name"="id", "required"=true, "requirement"="\d+", "dataType"="integer", "description"= "rabais_offre Id"}
     *  },
     * input={
     *     "class"="APM\VenteBundle\Entity\Rabais_offre",
     *     "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\ValidationParser"
     *      },
     *    "name" = "Rabais_offre",
     * },
     *     views = {"default", "vente" }
     * )
     * @param Request $request
     * @param Offre $offre
     * @Post("/new/rabaisoffre/{id}")
     * @return View|JsonResponse
     */
    public function newAction(Request $request, Offre $offre)
    {
        try {
            $this->createSecurity($offre);
            /** @var Rabais_offre $rabais_offre */
            $rabais_offre = TradeFactory::getTradeProvider('rabais');
            $form = $this->createForm('APM\VenteBundle\Form\Rabais_offreType', $rabais_offre);
            $form->submit($request->request->all());
            if (!$form->isValid()) {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $this->createSecurity($offre, $rabais);
            $rabais_offre->setVendeur($this->getUser());
            $rabais_offre->setOffre($offre);
            $em = $this->getDoctrine()->getManager();
            $em->persist($rabais_offre);
            $em->flush();
            return $this->routeRedirectView("api_vente_show_rabais", ['id' => $rabais_offre->getId()], Response::HTTP_CREATED);
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
     * @param Rabais_offre|null $rabais
     */
    private function createSecurity($offre = null, $rabais = null)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        if ($offre) { /// N'autorise que le vendeur, le gerant ou le proprietaire à pouvoir  faire des rabais
            $user = $this->getUser();
            $boutique = $offre->getBoutique();
            $gerant = null;
            $proprietaire = null;
            $beneficiaire = null;
            if ($boutique) {
                $gerant = $boutique->getGerant();
                $proprietaire = $boutique->getProprietaire();
            }
            $vendeur = $offre->getVendeur();
            if ($rabais) $beneficiaire = $rabais->getBeneficiaireRabais();
            //le beneficiaire du rabais ne peut être celui qui le cree et le createur ne devrait être que le vendeur ayant droit,
            if ($user !== $gerant && $user !== $proprietaire && $user !== $vendeur || $beneficiaire === $user) {
                throw $this->createAccessDeniedException();
            }
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @ApiDoc(
     * resource=true,
     * description="Retrieve the details of an objet of type Rabais_offre.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * requirements = {
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="rabais_offre id"}
     * },
     * output={
     *   "class"="APM\VenteBundle\Entity\Rabais_offre",
     *   "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *    },
     *     "groups"={"owner_rabais_details", "owner_list"}
     * },
     * statusCodes={
     *     "output" = "A single Object",
     *     200="Returned when successful",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "vente"}
     * )
     * @param Rabais_offre $rabais_offre
     * @return JsonResponse
     *
     * @Get("/show/rabaisoffre/{id}")
     */
    public function showAction(Rabais_offre $rabais_offre)
    {
        $this->listAndShowSecurity($rabais_offre->getOffre(), $rabais_offre);
        $data = $this->get('apm_core.data_serialized')->getFormalData($rabais_offre, ["owner_rabais_details", "owner_list"]);
        return new JsonResponse($data, 200);
    }

    /**
     * @ApiDoc(
     * resource=true,
     * resourceDescription="Operations on Rabais_offre",
     * description="Update an object of type Rabais_offre.",
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
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="Rabais_offre Id"}
     * },
     * input={
     *    "class"="APM\VenteBundle\Entity\Rabais_offre",
     *     "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\ValidationParser"
     *      },
     *    "name" = "Rabais_offre",
     * },
     *
     * views = {"default", "vente" }
     * )
     * @param Request $request
     * @param Rabais_offre $rabais_offre
     * @return View|JsonResponse
     *
     * @Put("/edit/rabaisoffre/{id}")
     */
    public function editAction(Request $request, Rabais_offre $rabais_offre)
    {
        try {
            $this->editAndDeleteSecurity($rabais_offre);
            $form = $this->createForm('APM\VenteBundle\Form\Rabais_offreType', $rabais_offre);
            $form->submit($request->request->all(), false);
            if (!$form->isValid()) {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->routeRedirectView("api_vente_show_rabais", ['id' => $rabais_offre->getId()], Response::HTTP_OK);
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
     * @param Rabais_offre $rabais
     */
    private function editAndDeleteSecurity($rabais)
    {
        //---------------------------------security-----------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        $boutique = $rabais->getOffre()->getBoutique();
        $user = $this->getUser();
        $gerant = null;
        $proprietaire = null;
        if ($boutique) {
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
        }
        $vendeur = $rabais->getOffre()->getVendeur();
        if ($user !== $gerant && $user !== $proprietaire && $user !== $vendeur || $user === $rabais->getBeneficiaireRabais()) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------

    }


    /**
     * @ApiDoc(
     * resource=true,
     * description="Delete objet of type Rabais_offre.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * requirements = {
     *      {"name"="id", "dataType"="integer", "required"=true, "requirement"="\d+", "description"="rabais_offre Id"}
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
     * @param Rabais_offre $rabais_offre
     * @return View | JsonResponse
     *
     * @Delete("/delete/rabaisoffre/{id}")
     */
    public function deleteAction(Request $request, Rabais_offre $rabais_offre)
    {
        try {
            $this->editAndDeleteSecurity($rabais_offre);
            if (!$request->request->has('exec') || $request->request->get('exec') !== 'go') {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans('impossible de supprimer', [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $offre = $rabais_offre->getOffre();
            $em = $this->getDoctrine()->getManager();
            $em->remove($rabais_offre);
            $em->flush();
            return $this->routeRedirectView("api_vente_get_rabais_offre", [$offre->getId()], Response::HTTP_OK);
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
