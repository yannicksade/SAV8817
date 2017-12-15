<?php

namespace APM\TransportBundle\Controller;

use APM\TransportBundle\Entity\Profile_transporteur;
use APM\TransportBundle\Entity\Transporteur_zoneintervention;
use APM\TransportBundle\Entity\Zone_intervention;
use APM\TransportBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Patch;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Zone_intervention controller.
 * @RouteResource("zoneintervention")
 */
class Zone_interventionController extends FOSRestController
{
    private $code_filter;
    private $designation_filter;
    private $description_filter;
    private $adresse_filter;
    private $pays_filter;
    private $transporteur_filter;

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @Get("/cget/zoneinterventions", name="s")
     */
    public function getAction(Request $request)
    {
        try {
            $this->listeAndShowSecurity();
            /** @var Utilisateur_avm $user */
            $user = $this->getUser();
            $transporteur = $user->getTransporteur();
            $q = $request->query->get('q');
            $this->designation_filter = $request->request->has('designation_filter') ? $request->request->get('designation_filter') : "";
            $this->code_filter = $request->request->has('code_filter') ? $request->request->get('code_filter') : "";
            $this->description_filter = $request->request->has('description_filter') ? $request->request->get('description_filter') : "";
            $this->adresse_filter = $request->request->has('adresse_filter') ? $request->request->get('adresse_filter') : "";
            $this->pays_filter = $request->request->has('pays_filter') ? $request->request->get('pays_filter') : "";
            $this->transporteur_filter = $request->request->has('transporteur_filter') ? $request->request->get('transporteur_filter') : "";
            $iDisplayLength = $request->request->has('length') ? $request->request->get('length') : -1;
            $iDisplayStart = $request->request->has('start') ? intval($request->request->get('start')) : 0;
            $json = array();
            $zones = (null !== $transporteur) ? $transporteur->getZones() : null;
            if (null !== $zones) {
                $iTotalRecords = count($zones);
                if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
                $zones = $this->handleResults($zones, $iTotalRecords, $iDisplayStart, $iDisplayLength);
                $iFilteredRecords = count($zones);
                $data = $this->get('apm_core.data_serialized')->getFormalData($zones, array("owner_list"));
                $json['totalRecords'] = $iTotalRecords;
                $json['filteredRecords'] = $iFilteredRecords;
                $json['items'] = $data;
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

    private function listeAndShowSecurity()
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @param Collection $zones
     * @param $iTotalRecords
     * @param $iDisplayStart
     * @param $iDisplayLength
     * @return array
     */
    private function handleResults($zones, $iTotalRecords, $iDisplayStart, $iDisplayLength)
    {
        //filtering
        if ($zones === null) return array();

        if ($this->code_filter != null) {
            $zones = $zones->filter(function ($e) {//filtrage select
                /** @var Zone_intervention $e */
                return $e->getCode() === $this->code_filter;
            });
        }
        if ($this->pays_filter != null) {
            $zones = $zones->filter(function ($e) {//filtrage select
                /** @var Zone_intervention $e */
                return $e->getTransporteur() === $this->pays_filter;
            });
        }

        if ($this->transporteur_filter != null) {
            $zones = $zones->filter(function ($e) {//filter with the begining of the entering word
                /** @var Zone_intervention $e */
                $str1 = $e->getTransporteur()->getCode();
                $str2 = $this->transporteur_filter;
                $len = strlen($str2);
                return strncasecmp($str1, $str2, $len) === 0 ? true : false;
            });
        }
        if ($this->designation_filter != null) {
            $zones = $zones->filter(function ($e) {//search for occurences in the text
                /** @var Zone_intervention $e */
                $subject = $e->getDesignation();
                $pattern = $this->designation_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }
        if ($this->description_filter != null) {
            $zones = $zones->filter(function ($e) {//search for occurences in the text
                /** @var Zone_intervention $e */
                $subject = $e->getDescription();
                $pattern = $this->designation_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }
        if ($this->adresse_filter != null) {
            $zones = $zones->filter(function ($e) {//search for occurences in the text
                /** @var Zone_intervention $e */
                $subject = $e->getDescription();
                $pattern = $this->adresse_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }

        $zones = ($zones !== null) ? $zones->toArray() : [];
        //assortment: descending of date -- du plus recent au plus ancient
        usort(
            $zones, function ($e1, $e2) {
            /**
             * @var Zone_intervention $e1
             * @var Zone_intervention $e2
             */
            $dt1 = $e1->getDateEnregistrement()->getTimestamp();
            $dt2 = $e2->getDateEnregistrement()->getTimestamp();
            return $dt1 <= $dt2 ? 1 : -1;
        });
        if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
        //paging; slice and preserve keys' order
        $zones = array_slice($zones, $iDisplayStart, $iDisplayLength, true);

        return $zones;
    }

    /**
     * @param Request $request
     * @return View | JsonResponse
     * @Post("/new/zoneintervention/{id}")
     */
    public function newAction(Request $request)
    {
        try {
            $this->createSecurity();
            /** @var Zone_intervention $zone_intervention */
            $zone_intervention = TradeFactory::getTradeProvider("zone_intervention");
            $form = $this->createForm('APM\TransportBundle\Form\Zone_interventionType', $zone_intervention);
            $form->submit($request->request->all());
            if (!$form->isValid()) {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $transporteur = $form->get('transporteur')->getData();
            $this->createSecurity($transporteur);
            if (!$transporteur) $zone_intervention->setTransporteur($this->getUser()->getTransporteur());
            $em = $this->getDoctrine()->getManager();
            $em->persist($zone_intervention);
            $em->flush();

            return $this->routeRedirectView("api_transport_show_zoneintervention", ['id' => $zone_intervention->getId()], Response::HTTP_CREATED);

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
     */
    private function createSecurity($transporteur = null)
    {
        //---------------------------------security-----------------------------------------------
        $this->denyAccessUnlessGranted(['ROLE_TRANSPORTEUR', 'ROLE_BOUTIQUE'], null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $gerant = null;
        $proprietaire = null;
        if ($transporteur) {
            $user = $this->getUser();
            $livreur = $transporteur->getLivreurBoutique();
            if ($livreur) {
                $boutique = $livreur->getBoutiqueProprietaire();
                $gerant = $boutique->getGerant();
                $proprietaire = $boutique->getProprietaire();
                if ($user !== $gerant && $user !== $proprietaire) {
                    throw $this->createAccessDeniedException();
                }
            }
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * Finds and displays a Zone_intervention entity.
     * @param Zone_intervention $zone_intervention
     * @return JsonResponse
     *
     * @Get("/show/zoneintervention/{id}")
     */
    public function showAction(Zone_intervention $zone_intervention)
    {
        $this->listeAndShowSecurity();
        $data = $this->get('apm_core.data_serialized')->getFormalData($zone_intervention, ["owner_zone_details", "owner_list"]);
        return new JsonResponse($data, 200);
    }

    /**
     * Displays a form to edit an existing Zone_intervention entity.
     * @param Request $request
     * @param Zone_intervention $zone_intervention
     * @return View | JsonResponse
     * @Put("/edit/zoneintervention/{id}")
     */
    public function editAction(Request $request, Zone_intervention $zone_intervention)
    {
        try {
            $this->editAndDeleteSecurity($zone_intervention);
            $form = $this->createForm('APM\TransportBundle\Form\Zone_interventionType', $zone_intervention);
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

            return $this->routeRedirectView("api_transport_show_zoneintervention", ['id' => $zone_intervention->getId()], Response::HTTP_OK);

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
     * @param Zone_intervention $zone_intervention
     */
    private function editAndDeleteSecurity($zone_intervention)
    { //
        //--------------------------------- security: uniquement la boutique ou le le Transporteur autonome peut modifier et supprimer les ZI -----------------------------------------------
        $this->denyAccessUnlessGranted(['ROLE_TRANSPORTEUR', 'ROLE_BOUTIQUE'], null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $user = $this->getUser();
        $transporteur = $zone_intervention->getTransporteur();
        $livreur = $transporteur->getLivreurBoutique();
        if ($livreur) {
            $boutique = $livreur->getBoutiqueProprietaire();
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
            if ($user !== $gerant && $user !== $proprietaire) {
                throw $this->createAccessDeniedException();
            }

        } else if ($user !== $transporteur->getUtilisateur()) {
            throw $this->createAccessDeniedException();
        }

        //----------------------------------------------------------------------------------------
    }


    /**
     * Deletes a Zone_intervention entity.
     * @param Request $request
     * @param Zone_intervention $zone_intervention
     * @return View | JsonResponse
     *
     * @Delete("/delete/zoneintervention/{id}")
     */
    public function deleteAction(Request $request, Zone_intervention $zone_intervention)
    {
        try {
            $this->editAndDeleteSecurity($zone_intervention);
            if (!$request->request->has('exec') || $request->request->get('exec') !== 'go') {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans('impossible de supprimer', [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $em = $this->getDoctrine()->getManager();
            $em->remove($zone_intervention);
            $em->flush();
            return $this->routeRedirectView("api_transport_get_zoneinterventions", [], Response::HTTP_OK);
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
