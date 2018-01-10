<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 02/04/2017
 * Time: 10:07
 */

namespace APM\TransportBundle\Controller;

use APM\TransportBundle\Entity\Profile_transporteur;
use APM\TransportBundle\Entity\Transporteur_zoneintervention;
use APM\TransportBundle\Entity\Zone_intervention;
use APM\TransportBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class Transporteur_zoneInterventionController
 * @RouteResource("transporteur-zoneIntervention", pluralize=false)
 */
class Transporteur_zoneInterventionController extends FOSRestController
{
    private $transporteur_filter;
    private $zoneIntervention_filter;

    /**
     * @ApiDoc(
     * resource=true,
     * description="Retrieve list of Transporteur_zoneIntervention.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * filters={
     *      {"name"="transporteur_filter", "dataType"="string"},
     *      {"name"="zoneIntervention_filter", "dataType"="string"},
     *      {"name"="length", "dataType"="integer", "requirement"="\d+"},
     *      {"name"="start", "dataType"="integer", "requirement"="\d+"},
     *  },
     *
     * output={
     *   "class"="APM\TransportBundle\Entity\Transporteur_zoneintervention",
     *   "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *    },
     *     "groups"={"owner_list"}
     * },
     * statusCodes={
     *     "output" = "A single or a collection of Transporteur_zoneIntervention",
     *     200="Returned when successful",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "transport"}
     * )
     * @ParamConverter("zone_intervention", options={"mapping": {"zone_id":"id"}})
     * @param Request $request
     * @param Profile_transporteur $transporteur
     * @param Zone_intervention $zone_intervention
     * @return JsonResponse
     *
     * @Get("/cget/transporteurs/zone/{zone_id}", name="s_zone", requirements={"zoneIntervention_id"="\d+"})
     * @Get("/cget/zones/transporteur/{id}", name="s_transporteur", requirements={"id"="transporteur_id"})
     */
    public function getAction(Request $request, Profile_transporteur $transporteur = null, Zone_intervention $zone_intervention = null)
    {
        try {
            $this->listeAndShowSecurity();
            if (null !== $transporteur) {
                $transporteurs_zones = $transporteur->getTransporteurZones();
            } else if (null !== $zone_intervention) {
                $transporteurs_zones = $zone_intervention->getZoneTransporteurs();
            }
            $json = array();
            $this->transporteur_filter = $request->request->has('transporteur_filter') ? $request->request->get('transporteur_filter') : "";
            $this->zoneIntervention_filter = $request->request->has('zoneIntervention_filter') ? $request->request->get('zoneIntervention_filter') : "";
            $iDisplayLength = $request->request->has('length') ? intval($request->request->get('length')) : -1;
            $iDisplayStart = $request->request->has('start') ? intval($request->request->get('start')) : 0;
            $iTotalRecords = count($transporteurs_zones);
            if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
            $transporteurs_zones = $this->handleResults($transporteurs_zones, $iTotalRecords, $iDisplayStart, $iDisplayLength);
            $iFilteredRecords = count($transporteurs_zones);
            $data = $this->get('apm_core.data_serialized')->getFormalData($transporteurs_zones, array("owner_list"));
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

    private function listeAndShowSecurity()
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        //-----------------------------------------------------------------------------------------
    }

    /**
     * @param Collection $transporteurs_zones
     * @param $iTotalRecords
     * @param $iDisplayStart
     * @param $iDisplayLength
     * @return array
     */
    private function handleResults($transporteurs_zones, $iTotalRecords, $iDisplayStart, $iDisplayLength)
    {
        //filtering
        if ($transporteurs_zones === null) return array();
        if ($this->transporteur_filter != null) {
            $transporteurs_zones = $transporteurs_zones->filter(function ($e) {//search for occurences in the text
                /** @var Transporteur_zoneintervention $e */
                $subject = $e->getTransporteur()->getUtilisateur()->getUsername();
                $pattern = $this->transporteur_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }
        if ($this->zoneIntervention_filter != null) {
            $transporteurs_zones = $transporteurs_zones->filter(function ($e) {//search for occurences in the text
                /** @var Transporteur_zoneintervention $e */
                $subject = $e->getZoneIntervention()->getDesignation();
                $pattern = $this->zoneIntervention_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }
        $transporteurs_zones = ($transporteurs_zones !== null) ? $transporteurs_zones->toArray() : [];
        //assortment: descending of date -- du plus recent au plus ancient
        usort(
            $transporteurs_zones, function ($e1, $e2) {
            /**
             * @var  Transporteur_zoneintervention $e1
             * @var  Transporteur_zoneintervention $e2
             */
            $dt1 = $e1->getDateEnregistrement()->getTimestamp();
            $dt2 = $e2->getDateEnregistrement()->getTimestamp();
            return $dt1 <= $dt2 ? 1 : -1;
        });
        if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
        //paging; slice and preserve keys' order
        $transporteurs_zones = array_slice($transporteurs_zones, $iDisplayStart, $iDisplayLength, true);

        return $transporteurs_zones;
    }

    /**
     * @ApiDoc(
     * resource=true,
     * resourceDescription="Operations on transporteur zoneIntervention.",
     * description="Create an object of type Transporteur_zoneIntervention.",
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
     *          "ROLE_TRANSPORTEUR"
     *     },
     * input={
     *     "class"="APM\TransportBundle\Form\Transporteur_zoneInterventionType",
     *     "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\FormTypeParser"
     *      }
     * },
     * requirements={
     *      {"name"="id", "required"=true, "requirement"="\d+", "dataType"="integer", "description"="transporteur Id"},
     *      {"name"="zone_id", "required"=true, "requirement"="\d+", "dataType"="integer", "description"="ZoneIntervention Id"}
     * },
     *      views = {"default", "transport" }
     * )
     * @param Request $request
     * @param Profile_transporteur $transporteur
     * @param Zone_intervention|null $zone_intervention
     * @return View | JsonResponse
     *
     * @Post("/new/transporteur/{id}/zone/{zone_id}", name="_transporteur_zone")
     *
     */
    public function newAction(Request $request, Profile_transporteur $transporteur, Zone_intervention $zone_intervention)
    {
        try {
            $this->createSecurity($transporteur);
            /** @var Transporteur_zoneintervention $transporteur_zoneIntervention */
            $transporteur_zoneIntervention = TradeFactory::getTradeProvider('transporteur_zoneIntervention');
            $form = $this->createForm('APM\TransportBundle\Transporteur_zoneInterventionType', $transporteur_zoneIntervention);
            $data = $request->request->has($form->getName()) ? $request->request->get($form->getName()) : $data[$form->getName()] = array();
            $form->submit($data);
            if (!$form->isValid()) {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            if (null !== $zone_intervention) $transporteur_zoneIntervention->setZoneIntervention($zone_intervention);
            if (null !== $transporteur) $transporteur_zoneIntervention->setTransporteur($transporteur);
            $em = $this->getEM();
            $em->persist($transporteur_zoneIntervention);
            $em->flush();

            return $this->routeRedirectView("api_transport_show_transporteur-zoneintervention ", ['id' => $transporteur_zoneIntervention->getId()], Response::HTTP_CREATED);

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
     * @param Profile_transporteur $transporteur
     *
     */
    private function createSecurity($transporteur)
    {
        //----------------security: Ajouter par le proprietaire, le gerant boutique ou le transporteur freelance-------------------
        $this->denyAccessUnlessGranted(['ROLE_TRANSPORTEUR', 'ROLE_BOUTIQUE'], null, 'Unable to access this page!');
        /** @var Utilisateur_avm $user */
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        $user = $this->getUser();
        if (null === $transporteur) $transporteur = $user->getTransporteur();
        if ($user !== $transporteur->getUtilisateur()) throw $this->createAccessDeniedException();
        $livreur_boutique = $transporteur->getLivreurBoutique();
        if (null !== $livreur_boutique) {
            $boutique = $livreur_boutique->getBoutiqueProprietaire();
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
            if ($gerant !== $user && $proprietaire !== $user) {
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
     * description="Retrieve the details of an objet of type Transporteur_zoneIntervention.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * requirements = {
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="Transporteur_zoneIntervention_Id"}
     * },
     * output={
     *   "class"="APM\TransportBundle\Entity\Transporteur_zoneintervention",
     *   "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *    },
     *     "groups"={"owner_transporteurZ_details", "owner_list"}
     * },
     * statusCodes={
     *     "output" = "A single Object",
     *     200="Returned when successful",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "transport"}
     * )
     *
     * @param Transporteur_zoneintervention $transporteur_zoneintervention
     * @return JsonResponse
     *
     * @Get("/show/transporteur-zone/{id}")
     */
    public function showAction(Transporteur_zoneintervention $transporteur_zoneintervention)
    {
        $this->editAndDeleteSecurity($transporteur_zoneintervention);
        $data = $this->get('apm_core.data_serialized')->getFormalData($transporteur_zoneintervention, ["owner_transporteurZ_details", "owner_list"]);
        return new JsonResponse($data, 200);
    }

    /**
     * @param Transporteur_zoneintervention $transporteur_zoneintervention
     */
    private function editAndDeleteSecurity($transporteur_zoneintervention)
    {
        //------------------------security: Modifier ou supprimme par le gerant boutique ou le transporteur freelance-----------------
        // Unable to access the controller unless they have the required role
        $this->denyAccessUnlessGranted(['ROLE_TRANSPORTEUR', 'ROLE_BOUTIQUE'], null, 'Unable to access this page!');
        /** @var Utilisateur_avm $user */
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $user = $this->getUser();
        $livreur_boutique = $transporteur_zoneintervention->getTransporteur()->getLivreurBoutique();
        if ($livreur_boutique) {
            $boutique = $livreur_boutique->getBoutiqueProprietaire();
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
            if ($gerant !== $user && $proprietaire !== $user) {
                throw $this->createAccessDeniedException();
            }

        } else if ($user !== $transporteur->getUtilisateur()) throw $this->createAccessDeniedException();

        //---------------------------------------------------------------------------------------------------------------------------------
    }

    /**
     * @ApiDoc(
     * resource=true,
     * resourceDescription="Operations on Transporteur_zoneintervention",
     * description="Update an object of type Transporteur_zoneintervention.",
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
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="transporteur_zoneintervention Id"}
     * },
     * authentication= true,
     * authenticationRoles= {
     *          "ROLE_TRANSPORTEUR",
     *           "ROLE_BOUTIQUE"
     *     },
     * input={
     *     "class"="APM\TransportBundle\Form\Transporteur_zoneInterventionType",
     *     "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\FormTypeParser"
     *      }
     * },
     * views = {"default", "transport" }
     * )
     * @param Request $request
     * @param Transporteur_zoneintervention $transporteur_zoneintervention
     * @return JsonResponse| View
     *
     * @Put("/edit/transporteur-zone/{id}")
     */
    public function editAction(Request $request, Transporteur_zoneintervention $transporteur_zoneintervention)
    {
        try {
            $this->editAndDeleteSecurity($transporteur_zoneintervention);
            $form = $this->createForm('APM\TransportBundle\Form\Transporteur_zoneInterventionType', $transporteur_zoneintervention);
            $form->submit($request->request->all(), false);
            if (!$form->isValid()) {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
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
     * @ApiDoc(
     * resource=true,
     * description="Delete objet of type Transporteur_zoneintervention.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * requirements = {
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="transporteur_zoneintervention Id"}
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
     * @param Transporteur_zoneintervention $transporteur_zoneintervention
     * @return View | JsonResponse
     *
     * @Post("/delete/transporteur-zone/{id}")
     */
    public function deleteAction(Request $request, Transporteur_zoneintervention $transporteur_zoneintervention)
    {
        try {
            $this->editAndDeleteSecurity($transporteur_zoneintervention);
            if (!$request->request->has('exec') || $request->request->get('exec') !== 'go') {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans('impossible de supprimer', [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $em = $this->getEM();
            $em->remove($transporteur_zoneintervention);
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
