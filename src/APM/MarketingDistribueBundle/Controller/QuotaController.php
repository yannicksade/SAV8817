<?php

namespace APM\MarketingDistribueBundle\Controller;

use APM\MarketingDistribueBundle\Entity\Quota;
use APM\MarketingDistribueBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Boutique;
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
 * Quota controller.
 * @RouteResource("commission")
 */
class QuotaController extends FOSRestController
{
    private $valeurQuota_filter;
    private $code_filter;
    private $dateFrom_filter;
    private $dateTo_filter;
    private $libelle_filter;
    private $description_filter;
    private $boutique_filter;
    private $valeurQuotaFrom_filter;
    private $valeurQuotaTo_filter;

    /**
     * @ApiDoc(
     * resource=true,
     * description="Retrieve list of Commissions.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * filters={
     *      {"name"="code_filter", "dataType"="string"},
     *      {"name"="dateFrom_filter", "dataType"="dateTime", "pattern"="19-12-2017|ASC"},
     *      {"name"="dateTo_filter", "dataType"="dateTime", "pattern"="19-12-2017|DESC"},
     *      {"name"="boutique_filter", "dataType"="string"},
     *      {"name"="libelle_filter", "dataType"="string"},
     *      {"name"="description_filter", "dataType"="string"},
     *      {"name"="valeurQuota_filter", "dataType"="integer"},
     *      {"name"="length", "dataType"="integer", "requirement"="\d+"},
     *      {"name"="start", "dataType"="integer", "requirement"="\d+"},
     *  },
     * requirements = {
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="boutique Id"}
     * },
     * output={
     *   "class"="APM\MarketingDistribueBundle\Entity\Conseiller",
     *   "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *    },
     *     "groups"={"owner_list"}
     * },
     * statusCodes={
     *     "output" = "A single or a collection of Commission",
     *     200="Returned when successful",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "marketing"}
     * )
     * @param Request $request
     * @param Boutique $boutique
     * @return JsonResponse
     *
     * @Get("/cget/commissions/boutique/{id}", name="s_boutique")
     */
    public function getAction(Request $request, Boutique $boutique)
    {
        try {
            $this->listAndShowSecurity($boutique);
            $quotas = $boutique->getCommissionnements();
            $this->valeurQuota_filter = $request->query->has('valeurQuota_filter') ? $request->query->get('valeurQuota_filter') : "";
            $this->code_filter = $request->query->has('code_filter') ? $request->query->get('code_filter') : "";
            $this->dateFrom_filter = $request->query->has('dateFrom_filter') ? $request->query->get('dateFrom_filter') : "";
            $this->dateTo_filter = $request->query->has('dateTo_filter') ? $request->query->get('dateTo_filter') : "";
            $this->libelle_filter = $request->query->has('libelle_filter') ? $request->query->get('libelle_filter') : "";
            $this->description_filter = $request->query->has('description_filter') ? $request->query->get('description_filter') : "";
            $this->boutique_filter = $request->query->has('boutique_filter') ? $request->query->get('boutique_filter') : "";
            $iDisplayLength = $request->query->has('length') ? intval($request->query->get('length')) : -1;
            $iDisplayStart = $request->query->has('start') ? intval($request->query->get('start')) : 0;
            $json = array();
            $iTotalRecords = count($quotas);
            if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
            $quotas = $this->handleResults($quotas, $iTotalRecords, $iDisplayStart, $iDisplayLength);
            $iFilteredRecords = count($quotas);
            $data = $this->get('apm_core.data_serialized')->getFormalData($quotas, array("owner_list"));
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
     */
    private function listAndShowSecurity($boutique = null)
    {
        //-----------------------------------security-----------------------------------------------------------
        // Unable to access the controller unless are the owner or you have the CONSEILLER role
        // Le Conseiller et la boutique à le droit de lister tous les quotas
        $this->denyAccessUnlessGranted(['ROLE_CONSEILLER', 'ROLE_BOUTIQUE'], null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $conseiller = $user->getProfileConseiller();
        $gerant = null;
        $proprietaire = null;
        if ($boutique) {
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
        }
        if (null === $conseiller && $user !== $gerant && $user !== $proprietaire) throw $this->createAccessDeniedException();

        //------------------------------------------------------------------------------------------------------
    }

    /**
     * @param Collection $commissions
     * @param $iTotalRecords
     * @param $iDisplayStart
     * @param $iDisplayLength
     * @return array
     */
    private function handleResults($commissions, $iTotalRecords, $iDisplayStart, $iDisplayLength)
    {
        //filtering
        if ($commissions === null) return array();

        if ($this->code_filter != null) {
            $commissions = $commissions->filter(function ($e) {//filtrage select
                /** @var Quota $e */
                return $e->getCode() === $this->code_filter;
            });
        }
        if ($this->boutique_filter != null) {
            $commissions = $commissions->filter(function ($e) {//filtrage select
                /** @var Quota $e */
                return $e->getBoutiqueProprietaire()->getCode() === $this->boutique_filter;
            });
        }
        if ($this->valeurQuotaFrom_filter != null) {
            $commissions = $commissions->filter(function ($e) {//filtrage select
                /** @var Quota $e */
                return $e->getValeurQuota() <= $this->valeurQuotaFrom_filter;
            });
        }
        if ($this->valeurQuotaTo_filter != null) {
            $commissions = $commissions->filter(function ($e) {//filtrage select
                /** @var Quota $e */
                return $e->getValeurQuota() >= $this->valeurQuotaTo_filter;
            });
        }
        if ($this->dateFrom_filter != null) {
            $commissions = $commissions->filter(function ($e) {//start date
                /** @var Quota $e */
                $dt1 = (new \DateTime($e->getDate()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateFrom_filter))->getTimestamp();
                return $dt1 - $dt2 >= 0 ? true : false; //start from the given date 'dateFrom_filter'
            });
        }
        if ($this->dateTo_filter != null) {
            $commissions = $commissions->filter(function ($e) {//end date
                /** @var Quota $e */
                $dt = (new \DateTime($e->getDate()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateTo_filter))->getTimestamp();
                return $dt - $dt2 <= 0 ? true : false;// end from at the given date 'dateTo_filter'
            });
        }
        if ($this->libelle_filter != null) {
            $commissions = $commissions->filter(function ($e) {//search for occurences in the text
                /** @var Quota $e */
                $subject = $e->getLibelleQuota();
                $pattern = $this->libelle_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }
        if ($this->description_filter != null) {
            $commissions = $commissions->filter(function ($e) {//search for occurences in the text
                /** @var Quota $e */
                $subject = $e->getDescription();
                $pattern = $this->description_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }

        $commissions = ($commissions !== null) ? $commissions->toArray() : [];
        //assortment: descending of date -- du plus recent au plus ancient
        usort(
            $commissions, function ($e1, $e2) {
            /**
             * @var Quota $e1
             * @var Quota $e2
             */
            $dt1 = $e1->getDate()->getTimestamp();
            $dt2 = $e2->getDate()->getTimestamp();
            return $dt1 <= $dt2 ? 1 : -1;
        });

        if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
        //paging; slice and preserve keys' order
        $commissions = array_slice($commissions, $iDisplayStart, $iDisplayLength, true);

        return $commissions;
    }

    /**
     * @ApiDoc(
     * resource=true,
     * resourceDescription="Operations on Commission",
     * description="Create an object of type Commission",
     * statusCodes={
     *         201="Returned when successful",
     *         400="Returned when the data are not valid or an unknown error occurred",
     *         403="Returned when the user is not authorized to carry on the action",
     *         404="Returned when the entity is not found",
     * },
     * headers={
     *      { "name"="Authorization", "required"=true, "description"="Authorization token"}
     * },
     * requirements = {
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="boutique Id"}
     * },
     * input={
     *    "class"="APM\MarketingDistribueBundle\Entity\Quota",
     *     "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\ValidationParser"
     *      },
     *    "name" = "Commission",
     * },
     *  views = {"default", "marketing" }
     * )
     * @param Request $request
     * @param Boutique $boutique
     * @return View | JsonResponse
     *
     * @Post("/new/commission/boutique/{id}", name="_boutique")
     */
    public function newAction(Request $request, Boutique $boutique)
    {
        try {
            $this->createSecurity($boutique);
            /** @var Quota $quotum */
            $quotum = TradeFactory::getTradeProvider("quota");
            $form = $this->createForm('APM\MarketingDistribueBundle\Form\QuotaType', $quotum);
            $form->submit($request->request->all());
            if (!$form->isValid()) {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $quotum->setBoutiqueProprietaire($boutique);
            $em = $this->getEM();
            $em->persist($quotum);
            $em->flush();

            return $this->routeRedirectView("api_marketing_show_commission", ['id' => $quotum->getId()], Response::HTTP_CREATED);

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
    private function createSecurity($boutique = null)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_BOUTIQUE', null, 'Unable to access this page!');
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

    private function getEM()
    {
        return $this->get('doctrine.orm.entity_manager');
    }

    /**
     * @ApiDoc(
     * resource=true,
     * description="Retrieve the details of an objet of type commission",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * requirements = {
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="commission_id"}
     * },
     * output={
     *   "class"="APM\MarketingDistribueBundle\Entity\Quota",
     *   "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *    },
     *     "groups"={"owner_quota_details", "owner_list"}
     * },
     * statusCodes={
     *     "output" = "A single Object",
     *     200="Returned when successful",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "marketing"}
     * )
     * @param Quota $quotum
     * @return JsonResponse
     *
     * @Get("/show/commission/{id}")
     */
    public function showAction(Quota $quotum)
    {
        $this->listAndShowSecurity($quotum->getBoutiqueProprietaire());
        $data = $this->get('apm_core.data_serialized')->getFormalData($quotum, ["owner_quota_details", "owner_list"]);
        return new JsonResponse($data, 200);
    }

    /**
     * @ApiDoc(
     * resource=true,
     * resourceDescription="Operations on quota",
     * description="Update an object of type Quota",
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
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="commission Id"}
     * },
     * input={
     *    "class"="APM\MarketingDistribueBundle\Entity\Quota",
     *     "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\ValidationParser"
     *      },
     *    "name" = "Quota",
     * },
     *     views={"default","marketing"}
     * )
     * @param Request $request
     * @param Quota $quotum
     * @return View | JsonResponse
     *
     * @Put("/edit/commission/{id}")
     */
    public function editAction(Request $request, Quota $quotum)
    {
        try {
            $this->editAndDeleteSecurity($quotum);
            $form = $this->createForm('APM\MarketingDistribueBundle\Form\QuotaType', $quotum);
            $form->submit($request->request->all(), false);
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
            return $this->routeRedirectView("api_marketing_show_commission", [$quotum->getId()], Response::HTTP_OK);
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
     * @param Quota $quotum
     */
    private function editAndDeleteSecurity($quotum)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to Edit or delete unless you are the owner
        $this->denyAccessUnlessGranted('ROLE_BOUTIQUE', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $user = $this->getUser();
        $gerant = $quotum->getBoutiqueProprietaire()->getGerant();
        $proprietaire = $quotum->getBoutiqueProprietaire()->getProprietaire();
        if ($gerant !== $user && $user !== $proprietaire) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------

    }

    /**
     * @ApiDoc(
     * resource=true,
     * description="Delete objet of type Quota",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * requirements = {
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="quota Id"}
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
     * @param Quota $quotum
     * @return View | JsonResponse
     *
     * @delete("/delete/commission/{id}")
     */
    public function deleteAction(Request $request, Quota $quotum)
    {
        try {
            $this->editAndDeleteSecurity($quotum);
            if (!$request->request->has('exec') || $request->request->get('exec') !== 'go') {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans('impossible de supprimer', [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $boutique = $quotum->getBoutiqueProprietaire();
            $em = $this->getEM();
            $em->remove($quotum);
            $em->flush();

            return $this->routeRedirectView("api_marketing_get_commissions_boutique", [$boutique->getId()], Response::HTTP_OK);
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
