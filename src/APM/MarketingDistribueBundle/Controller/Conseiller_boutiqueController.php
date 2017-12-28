<?php

namespace APM\MarketingDistribueBundle\Controller;


use APM\MarketingDistribueBundle\Entity\Conseiller;
use APM\MarketingDistribueBundle\Entity\Conseiller_boutique;
use APM\MarketingDistribueBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Boutique;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Patch;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Conseiller_boutique controller.
 * @RouteResource("conseillerboutique")
 */
class Conseiller_boutiqueController extends FOSRestController
{
    private $gainValeur_filter;
    private $code_filter;
    private $dateTo_filter;
    private $dateFrom_filter;
    private $conseiller_filter;
    private $boutique_filter;

    /**
     * @ApiDoc(
     * resource=true,
     * description="Retrieve list of Conseiller_boutique.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * filters={
     *      {"name"="code_filter", "dataType"="string"},
     *      {"name"="dateFrom_filter", "dataType"="dateTime", "pattern"="19-12-2017|ASC"},
     *      {"name"="dateTo_filter", "dataType"="dateTime", "pattern"="19-12-2017|DESC"},
     *      {"name"="conseiller_filter", "dataType"="string"},
     *      {"name"="boutique_filter", "dataType"="string"},
     *      {"name"="gainValeur_filter", "dataType"="integer"},
     *      {"name"="length", "dataType"="integer", "requirement"="\d+"},
     *      {"name"="start", "dataType"="integer", "requirement"="\d+"},
     *  },
     *
     * output={
     *   "class"="APM\MarketingDistribueBundle\Entity\Conseiller_boutique",
     *   "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *    },
     *     "groups"={"owner_list"}
     * },
     * statusCodes={
     *     "output" = "A single or a collection of Conseiller_boutique",
     *     200="Returned when successful",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "marketing"}
     * )
     * @param Request $request
     * @param Boutique $boutique
     * @return  JsonResponse
     *
     * Lister les boutique du conseiller
     * @Get("/cget/conseiller-boutiques", name="s")
     * @Get("/cget/conseillers-boutique/boutique/{id}", name="s_boutique", requirements={"id"="boutique_id"})
     */
    public function getAction(Request $request, Boutique $boutique = null)
    {
        try {
            $this->listAndShowSecurity();
            if (null === $boutique) {
                /** @var Utilisateur_avm $user */
                $user = $this->getUser();
                $boutiques_conseillers = $user->getProfileConseiller()->getConseillerBoutiques();
            } else {
                $boutiques_conseillers = $boutique->getBoutiqueConseillers();
            }

            $json = array();
            $this->gainValeur_filter = $request->query->has('gainValeur_filter') ? $request->query->get('gainValeur_filter') : "";
            $this->code_filter = $request->query->has('code_filter') ? $request->query->get('code_filter') : "";
            $this->dateFrom_filter = $request->query->has('dateFrom_filter') ? $request->query->get('dateFrom_filter') : "";
            $this->dateTo_filter = $request->query->has('dateTo_filter') ? $request->query->get('dateTo_filter') : "";
            $this->conseiller_filter = $request->query->has('conseiller_filter') ? $request->query->get('conseiller_filter') : "";
            $this->boutique_filter = $request->query->has('boutique_filter') ? $request->query->get('boutique_filter') : "";
            $iDisplayLength = $request->query->has('length') ? intval($request->query->get('length')) : -1;
            $iDisplayStart = $request->query->has('start') ? intval($request->query->get('start')) : 0;
            $iTotalRecords = count($boutiques_conseillers);
            if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
            $boutiques_conseillers = $this->handleResults($boutiques_conseillers, $iTotalRecords, $iDisplayStart, $iDisplayLength);
            $iFilteredRecords = count($boutiques_conseillers);
            $data = $this->get('apm_core.data_serialized')->getFormalData($boutiques_conseillers, array("owner_list"));
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
     * @param Conseiller_boutique $conseiller_boutique
     */
    private function listAndShowSecurity($conseiller_boutique = null)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a CONSEILLER or BOUTIQUE role
        $this->denyAccessUnlessGranted(['ROLE_CONSEILLER', 'ROLE_BOUTIQUE'], null, 'Unable to access this page!');
        /* ensure that the user is logged in  # granted even through remembering cookies
        *  and that the one is the owner
        */
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw $this->createAccessDeniedException();
        }
        //Pour afficher les details des boutique affiliés
        if (null !== $conseiller_boutique) {
            $user = $this->getUser();
            $conseiller = $conseiller_boutique->getConseiller()->getUtilisateur();
            $proprietaire = $conseiller_boutique->getBoutique()->getProprietaire();
            $gerant = $conseiller_boutique->getBoutique()->getGerant();
            if ($conseiller !== $user && $user !== $gerant && $user !== $proprietaire) {
                throw $this->createAccessDeniedException();
            }
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @param Collection $boutiques_conseillers
     * @param $iTotalRecords
     * @param $iDisplayStart
     * @param $iDisplayLength
     * @return array
     */
    private function handleResults($boutiques_conseillers, $iTotalRecords, $iDisplayStart, $iDisplayLength)
    {
        //filtering
        if ($boutiques_conseillers === null) return array();

        if ($this->conseiller_filter != null) {
            $boutiques_conseillers = $boutiques_conseillers->filter(function ($e) {//filtrage select
                /** @var Conseiller_boutique $e */
                return $e->getConseiller()->getCode() === $this->conseiller_filter;
            });
        }
        if ($this->boutique_filter != null) {
            $boutiques_conseillers = $boutiques_conseillers->filter(function ($e) {//filtrage select
                /** @var Conseiller_boutique $e */
                return $e->getBoutique()->getCode() === $this->boutique_filter;
            });
        }

        if ($this->dateFrom_filter != null) {
            $boutiques_conseillers = $boutiques_conseillers->filter(function ($e) {//start date
                /** @var Conseiller_boutique $e */
                $dt1 = (new \DateTime($e->getDate()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateFrom_filter))->getTimestamp();
                return $dt1 - $dt2 >= 0 ? true : false; //start from the given date 'dateFrom_filter'
            });
        }
        if ($this->dateTo_filter != null) {
            $boutiques_conseillers = $boutiques_conseillers->filter(function ($e) {//end date
                /** @var Conseiller_boutique $e */
                $dt = (new \DateTime($e->getDate()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateTo_filter))->getTimestamp();
                return $dt - $dt2 <= 0 ? true : false;// end from at the given date 'dateTo_filter'
            });
        }
        $boutiques_conseillers = ($boutiques_conseillers !== null) ? $boutiques_conseillers->toArray() : [];
        //assortment: descending of date -- du plus recent au plus ancient
        usort(
            $boutiques_conseillers, function ($e1, $e2) {
            /**
             * @var Conseiller_boutique $e1
             * @var Conseiller_boutique $e2
             */
            $dt1 = $e1->getDate()->getTimestamp();
            $dt2 = $e2->getDate()->getTimestamp();
            return $dt1 <= $dt2 ? 1 : -1;
        });

        if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
        //paging; slice and preserve keys' order
        $boutiques_conseillers = array_slice($boutiques_conseillers, $iDisplayStart, $iDisplayLength, true);

        return $boutiques_conseillers;
    }

    /**
     * @ApiDoc(
     * resource=true,
     * resourceDescription="Operations on Conseiller_boutique.",
     * description="Create an object of type Conseiller_boutique.",
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
     *    "class"="APM\MarketingDistribueBundle\Entity\Conseiller_boutique",
     *     "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\ValidationParser"
     *      },
     *    "name" = "Conseiller_boutique",
     * },
     *  views = {"default", "marketing" }
     * )
     * @param Request $request
     * @param Boutique $boutique
     * @return View| JsonResponse
     *
     * @Post("/new/conseiller-boutique")
     * @Post("/new/conseiller-boutique/boutique/{id}", name="_boutique", requirements={"id"="boutique_id"})
     */
    public function newAction(Request $request, Boutique $boutique = null)
    {
        try {
            $this->createSecurity($boutique);
            /** @var Conseiller_boutique $conseiller_boutique */
            $conseiller_boutique = TradeFactory::getTradeProvider("conseiller_boutique");
            $form = $this->createForm('APM\MarketingDistribueBundle\Form\Conseiller_boutiqueType', $conseiller_boutique);
            if (null !== $boutique) $form->remove('boutique');
            $form->submit($request->request->all());
            if (!$form->isValid()) {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            /** @var Utilisateur_avm $user */
            $user = $this->getUser();
            $this->createSecurity($conseiller_boutique->getBoutique());
            if (null !== $boutique) $conseiller_boutique->setBoutique($boutique);
            $conseiller_boutique->setConseiller($user->getProfileConseiller());
            $em = $this->getDoctrine()->getManager();
            $em->persist($conseiller_boutique);
            $em->flush();

            return $this->routeRedirectView("api_marketing_show_conseillerboutique", ['id' => $conseiller_boutique->getId()], Response::HTTP_CREATED);

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
        // Vérifier que l'utilisateur courant est bel et bien le conseiller
        $this->denyAccessUnlessGranted('ROLE_CONSEILLER', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $conseiller = $user->getProfileConseiller();
        $oldConseiller = null;
        if ($boutique && null !== $conseiller) {//l'enregistrement devrait être unique
            $em = $this->getDoctrine()->getManager();
            $oldConseiller = $em->getRepository('APMMarketingDistribueBundle:Conseiller_boutique')
                ->findOneBy(['conseiller' => $conseiller, 'boutique' => $boutique]);
        }

        if (null === $conseiller || null !== $oldConseiller) throw $this->createAccessDeniedException();
        //----------------------------------------------------------------------------------------
    }

    /**
     * @ApiDoc(
     * resource=true,
     * description="Retrieve the details of an objet of type Conseiller_boutique",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * requirements = {
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="conseiller_boutique_id"}
     * },
     * output={
     *   "class"="APM\MarketingDistribueBundle\Entity\Conseiller_boutique",
     *   "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *    },
     *     "groups"={"owner_conseillerB_details", "owner_list"}
     * },
     * statusCodes={
     *     "output" = "A single Object",
     *     200="Returned when successful",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "marketing"}
     * )
     * @param Conseiller_boutique $conseiller_boutique
     * @return JsonResponse
     *
     * @Get("/show/conseiller-boutique/{id}")
     */
    public function showAction(Conseiller_boutique $conseiller_boutique)
    {
        $this->listAndShowSecurity($conseiller_boutique);
        $data = $this->get('apm_core.data_serialized')->getFormalData($conseiller_boutique, ["owner_conseillerB_details", "owner_list"]);
        return new JsonResponse($data, 200);
    }

    /**
     * @ApiDoc(
     * resource=true,
     * resourceDescription="Operations on conseiller_boutique",
     * description="Update an object of type Conseiller_boutique.",
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
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="conseiller_boutique Id"}
     * },
     * input={
     *    "class"="APM\MarketingDistribueBundle\Entity\Conseiller_boutique",
     *     "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\ValidationParser"
     *      },
     *    "name" = "Conseiller_boutique",
     * },
     *     views={"default","marketing"}
     * )
     * @param Request $request
     * @param Conseiller_boutique $conseiller_boutique
     * @return View | JsonResponse
     *
     * @Put("/edit/conseiller-boutique/{id}")
     */
    public function editAction(Request $request, Conseiller_boutique $conseiller_boutique)
    {
        try {
            $this->editAndDeleteSecurity($conseiller_boutique, $conseiller_boutique->getConseiller());
            $form = $this->createForm('APM\MarketingDistribueBundle\Form\Conseiller_boutiqueType', $conseiller_boutique);
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

            return $this->routeRedirectView("api_marketing_show_conseillerboutique", ['id' => $conseiller_boutique->getId()], Response::HTTP_OK);

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
     * @param Conseiller_boutique $conseiller_boutique
     * @param Conseiller $conseiller
     */
    private function editAndDeleteSecurity($conseiller_boutique, $conseiller)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a CONSEILLER role
        $this->denyAccessUnlessGranted('ROLE_CONSEILLER', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
            throw $this->createAccessDeniedException();

        $user = $this->getUser();
        if ($conseiller_boutique) {
            $conseiller = $conseiller_boutique->getConseiller()->getUtilisateur();
        } else {
            $grantedUser = $conseiller->getUtilisateur();
        }

        if ($conseiller !== $user && $user !== $grantedUser) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------

    }

    /**
     * @ApiDoc(
     * resource=true,
     * description="Delete objet of type Conseiller_boutique.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * requirements = {
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="conseiller boutique Id"}
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
     * @param Conseiller_boutique $conseiller_boutique
     * @return View | JsonResponse
     *
     * @Delete("/delete/conseiller-boutique/{id}")
     */
    public function deleteAction(Request $request, Conseiller_boutique $conseiller_boutique)
    {
        try {
            $this->editAndDeleteSecurity($conseiller_boutique, $conseiller_boutique->getConseiller());
            if (!$request->request->has('exec') || $request->request->get('exec') !== 'go') {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans('impossible de supprimer', [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $em = $this->getDoctrine()->getManager();
            $em->remove($conseiller_boutique);
            $em->flush();
            return $this->routeRedirectView("api_marketing_get_conseillerboutiques", [], Response::HTTP_OK);
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
