<?php

namespace APM\TransportBundle\Controller;

use APM\TransportBundle\Entity\Livreur_boutique;
use APM\TransportBundle\Entity\Profile_transporteur;
use APM\TransportBundle\Factory\TradeFactory;
use APM\VenteBundle\Entity\Boutique;
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
use FOS\RestBundle\Controller\Annotations\Patch;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Livreur_boutique controller.
 * @RouteResource("livreur", pluralize=false)
 */
class Livreur_boutiqueController extends FOSRestController
{
    private $reference_filter;
    private $transporteur_filter;
    private $boutique_filter;

    /**
     * @ApiDoc(
     * resource=true,
     * description="Retrieve list of type livreur boutique",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * filters={
     *      {"name"="transporteur_filter", "dataType"="string"},
     *      {"name"="reference_filter", "dataType"="string"},
     *      {"name"="boutique_filter", "dataType"="integer"},
     *      {"name"="length_filter", "dataType"="integer", "requirement"="\d+"},
     *      {"name"="start_filter", "dataType"="integer", "requirement"="\d+"},
     *  },
     *
     * output={
     * "class"="APM\TransportBundle\Entity\Livreur_boutique",
     * "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *    },
     *     "groups"={"owner_list"}
     * },
     *     requirements={
     *          {"name"="id", "dataType"="integer", "requirements"="\d+", "description"="boutique Id"},
     *          {"name"="q", "dataType"="string", "requirement"="\D+", "description"="query request: owner, guest ", "format"= "?q=owner | null"}
     *     },
     * statusCodes={
     *     "output" = "A single or a collection of Livreurs of a boutique",
     *     200="Returned when successful",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "transport"}
     * )
     * @param Boutique $boutique
     * @return JsonResponse
     *
     * @Get("/cget/livreurs/boutique/{id}", name="s_boutique")
     */
    public function getAction(Boutique $boutique)
    {
        try {
            $this->listeAndShowSecurity();
            $json = array();
            $json['items'] = array();
            $this->reference_filter = $request->query->has('reference_filter') ? $request->query->get('reference_filter') : "";
            $this->transporteur_filter = $request->query->has('transporteur_filter') ? $request->query->get('transporteur_filter') : "";
            $this->boutique_filter = $request->query->has('boutique_filter') ? $request->query->get('boutique_filter') : "";
            $iDisplayLength = $request->query->has('length') ? $request->query->get('length') : -1;
            $iDisplayStart = $request->query->has('start') ? intval($request->query->get('start')) : 0;
            $q = $request->query->has('q') ? $q = $request->query->get('q') : "all";
            if ($q === "guest" || $q === "all") {
                $livreurs = $boutique->getLivreurs();//livreurs étrangers: empruntés
                if (null !== $livreurs) {
                    $iTotalRecords = count($livreurs);
                    if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
                    $livreurs = $this->handleResults($livreurs, $iTotalRecords, $iDisplayStart, $iDisplayLength);
                    $iFilteredRecords = count($livreurs);
                    $data = $this->get('apm_core.data_serialized')->getFormalData($livreurs, array("others_list"));
                    $json['totalRecordsGuest'] = $iTotalRecords;
                    $json['filteredRecordsGuest'] = $iFilteredRecords;
                    $json['items'] = $data;
                }
            }
            if ($q === "owner" || $q === "all") {
                $livreurs = $boutique->getLivreurBoutiques();
                if (null !== $livreurs) {
                    $iTotalRecords = count($livreurs);
                    if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
                    $livreurs = $this->handleResults($livreurs, $iTotalRecords, $iDisplayStart, $iDisplayLength);
                    $iFilteredRecords = count($livreurs);
                    $data = $this->get('apm_core.data_serialized')->getFormalData($livreurs, array("owner_list"));
                    $json['totalRecordsOwner'] = $iTotalRecords;
                    $json['filteredRecordsOwner'] = $iFilteredRecords;
                    $json['items'] = $data;
                }
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
        // Unable to access the controller unless you have the role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @param Collection $livreurs
     * @param $iTotalRecords
     * @param $iDisplayStart
     * @param $iDisplayLength
     * @return array
     */
    private function handleResults($livreurs, $iTotalRecords, $iDisplayStart, $iDisplayLength)
    {
        //filtering
        if ($livreurs === null) return array();

        if ($this->reference_filter != null) {
            $livreurs = $livreurs->filter(function ($e) {//filtrage select
                /** @var Livreur_boutique $e */
                return $e->getReference() === $this->reference_filter;
            });
        }
        if ($this->transporteur_filter != null) {
            $livreurs = $livreurs->filter(function ($e) {//filtrage select
                /** @var Livreur_boutique $e */
                return $e->getTransporteur()->getMatricule() === $this->transporteur_filter;
            });
        }

        if ($this->boutique_filter != null) {
            $livreurs = $livreurs->filter(function ($e) {//search for occurences in the text
                /** @var Livreur_boutique $e */
                return $e->getBoutiqueProprietaire()->getCode() === $this->boutique_filter;
            });
        }

        $livreurs = ($livreurs !== null) ? $livreurs->toArray() : [];
        //assortment: descending of date -- du plus recent au plus ancient
        usort(
            $livreurs, function ($e1, $e2) {
            /**
             * @var Livreur_boutique $e1
             * @var Livreur_boutique $e2
             */
            $dt1 = $e1->getDateEnregistrement()->getTimestamp();
            $dt2 = $e2->getDateEnregistrement()->getTimestamp();
            return $dt1 <= $dt2 ? 1 : -1;
        });
        if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
        //paging; slice and preserve keys' order
        $livreurs = array_slice($livreurs, $iDisplayStart, $iDisplayLength, true);

        return $livreurs;
    }


    /**
     * @ApiDoc(
     * resource=true,
     * resourceDescription="Operations on Livreur_boutique.",
     * description="Create an object of type livreur boutique.",
     * statusCodes={
     *         201="Returned when successful",
     *         400="Returned when the data are not valid or an unknown error occurred",
     *         403="Returned when the user is not authorized to carry on the action",
     *         404="Returned when the entity is not found",
     * },
     * headers={
     *      { "name"="Authorization",  "required"=true, "description"="Authorization token"}
     * },
     * requirements={
     *      {"name"="id", "dataType"="integer","requirement"="\d+", "description"="boutique_id"},
     *      {"name"="transporteur_id", "dataType"="integer", "requirement"="\d+", "description"="transporteur Id"},
     *  },
     * input={
     *    "class"="APM\TransportBundle\Entity\Livreur_boutique",
     *     "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\ValidationParser"
     *      },
     *    "name" = "Livreur",
     * },
     *      views = {"default", "transport" }
     * )
     * @ParamConverter("transporteur", options={"mapping":{"transporteur_id":"id"}})
     * @param Request $request
     * @param Boutique $boutique
     * @param Profile_transporteur $transporteur
     * @return JsonResponse| View
     * @Post("/new/livreur/boutique/{id}/transporteur/{transporteur_id}")
     */
    public function newAction(Request $request, Boutique $boutique, Profile_transporteur $transporteur)
    {
        try {
            $this->createSecurity($boutique);
            /** @var Livreur_boutique $livreur_boutique */
            $livreur_boutique = TradeFactory::getTradeProvider("livreur_boutique");
            $form = $this->createForm('APM\TransportBundle\Form\Livreur_boutiqueType', $livreur_boutique);
            $form->submit($request->request->all());
            if (!$form->isValid()) {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            /** @var Profile_transporteur $transporteur */
            $livreur_boutique->setTransporteur($transporteur);
            $livreur_boutique->setBoutiqueProprietaire($boutique);
            $transporteur->setLivreurBoutique($livreur_boutique);
            $em = $this->getDoctrine()->getManager();
            $em->persist($livreur_boutique);
            $em->flush();
            return $this->routeRedirectView("api_transport_show_livreur", ['id' => $livreur_boutique->getId()], Response::HTTP_CREATED);
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
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_BOUTIQUE', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $user = $this->getUser();
        $gerant = $boutique->getGerant();
        $proprietaire = $boutique->getProprietaire();
        if ($user !== $gerant && $user !== $proprietaire) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @ApiDoc(
     * resource=true,
     * description="Retrieve the details of an objet of type livreur_boutique.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * requirements = {
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="livreur id"}
     * },
     * output={
     *   "class"="APM\TransportBundle\Entity\Livreur_boutique",
     *   "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *    },
     *     "groups"={"owner_livreurB_details", "owner_list"}
     * },
     * statusCodes={
     *     "output" = "A single Object",
     *     200="Returned when successful",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "transport"}
     * )
     * @param Livreur_boutique $livreur_boutique
     * @return JsonResponse
     *
     * @Get("/show/livreur/{id}")
     */
    public function showAction(Livreur_boutique $livreur_boutique)
    {
        $this->listeAndShowSecurity();
        $data = $this->get('apm_core.data_serialized')->getFormalData($livreur_boutique, ["owner_livreurB_details", "owner_list"]);
        return new JsonResponse($data, 200);
    }

    /**
     * @ApiDoc(
     * resource=true,
     * resourceDescription="Operations on Livreur_boutique",
     * description="Update an object of type Livreur_boutique.",
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
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="livreur_boutique Id"}
     * },
     * input={
     *    "class"="APM\TransportBundle\Entity\Livreur_boutique",
     *     "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\ValidationParser"
     *      },
     *    "name" = "Transport",
     * },
     *
     * views = {"default", "transport" }
     * )
     * @param Request $request
     * @param Livreur_boutique $livreur_boutique
     * @return View | JsonResponse
     *
     * @Post("/edit/livreur/{id}")
     */
    public function editAction(Request $request, Livreur_boutique $livreur_boutique)
    {
        try {
            $this->editAndDeleteSecurity($livreur_boutique);
            $form = $this->createForm('APM\TransportBundle\Form\Livreur_boutiqueType', $livreur_boutique);
            $form->submit($request->request->all(), false);
            if (!$form->isValid()) {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->routeRedirectView("api_transport_show_livreur", ['id' => $livreur_boutique->getId()], Response::HTTP_OK);
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
     * @param Livreur_boutique $livreur_boutique
     */
    private function editAndDeleteSecurity($livreur_boutique)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have
        $this->denyAccessUnlessGranted('ROLE_BOUTIQUE', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $user = $this->getUser();
        $boutique = $livreur_boutique->getBoutiqueProprietaire();
        $gerant = $boutique->getGerant();
        $proprietaire = $boutique->getProprietaire();
        if ($user !== $gerant && $user !== $proprietaire) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @ApiDoc(
     * resource=true,
     * description="Delete objet of type livreur boutique",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * requirements = {
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="livreur Id"}
     * },
     * parameters = {
     *      {"name"="exec", "required"=true, "dataType"="string", "requirement"="\D+", "description"="needed to check the origin of the request", "format"="exec=go"}
     * },
     * statusCodes={
     *         200="Returned when successful",
     *         400="Returned when the data are not valid or an unknown error occurred",
     *         403="Returned when the user is not authorized to carry on the action",
     *         404="Returned when the entity is not found",
     * },
     *     views={"default", "transport"}
     * )
     * @param Request $request
     * @param Livreur_boutique $livreur_boutique
     * @return View | JsonResponse
     *
     * @Delete("/delete/livreur/{id}")
     */
    public function deleteAction(Request $request, Livreur_boutique $livreur_boutique)
    {
        try {
            $this->editAndDeleteSecurity($livreur_boutique);
            if (!$request->request->has('exec') || $request->request->get('exec') !== 'go') {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans('impossible de supprimer', [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $boutique = $livreur_boutique->getBoutiqueProprietaire();
            $em = $this->getDoctrine()->getManager();
            $em->remove($livreur_boutique);
            $em->flush();
            return $this->routeRedirectView("api_transport_get_livreurs_boutique", [$boutique->getId()], Response::HTTP_OK);
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
