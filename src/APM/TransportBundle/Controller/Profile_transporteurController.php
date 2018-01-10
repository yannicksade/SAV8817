<?php

namespace APM\TransportBundle\Controller;

use APM\TransportBundle\Entity\Livreur_boutique;
use APM\TransportBundle\Entity\Profile_transporteur;
use APM\TransportBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use Doctrine\Common\Collections\ArrayCollection;
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
 * Profile_transporteur controller.
 * @RouteResource("transporteur", pluralize=false)
 */
class Profile_transporteurController extends FOSRestController
{
    private $matricule_filter;
    private $code_filter;
    private $livreur_boutique;
    private $isLivreur_boutique_filter;

    /**
     * @ApiDoc(
     * resource=true,
     * description="Retrieve list of transporteurs.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"}
     * },
     * filters={
     *      {"name"="code_filter", "dataType"="string"},
     *      {"name"="livreur_boutique", "dataType"="string"},
     *      {"name"="matricule_filter", "dataType"="string"},
     *      {"name"="length", "dataType"="integer", "requirement"="\d+"},
     *      {"name"="start", "dataType"="integer", "requirement"="\d+"},
     *  },
     *
     * output={
     *   "class"="APM\TransportBundle\Entity\Profile_transporteur",
     *   "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *    },
     *     "groups"={"owner_list"}
     * },
     * statusCodes={
     *     "output" = "A single or a collection of transporteurs",
     *     200="Returned when successful",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "transport"}
     * )
     * @param Request $request
     * @return JsonResponse
     *
     * @Get("/cget/transporteurs", name="s")
     */
    public function getAction(Request $request)
    {
        try {
            $this->listeAndShowSecurity();
            $em = $this->getEM();
            $transporteurs = $em->getRepository('APMTransportBundle:Profile_transporteur')->findAll();
            $profile_transporteurs = new ArrayCollection($transporteurs);
            $json = array();
            $this->matricule_filter = $request->query->has('matricule_filter') ? $request->query->get('matricule_filter') : "";
            $this->code_filter = $request->query->has('code_filter') ? $request->query->get('code_filter') : "";
            $this->livreur_boutique = $request->query->has('livreur_boutique') ? $request->query->get('livreur_boutique') : "";
            $iDisplayLength = $request->query->has('length') ? intval($request->query->get('length')) : -1;
            $iDisplayStart = $request->query->has('start') ? intval($request->query->get('start')) : 0;
            $iTotalRecords = count($profile_transporteurs);
            if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
            $profile_transporteurs = $this->handleResults($profile_transporteurs, $iTotalRecords, $iDisplayStart, $iDisplayLength);
            $iFilteredRecords = count($profile_transporteurs);
            $data = $this->get('apm_core.data_serialized')->getFormalData($profile_transporteurs, array("owner_list"));
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
        //----------------------------------------------------------------------------------------
    }

    private function getEM()
    {
        return $this->get('doctrine.orm.entity_manager');
    }

    /**
     * @param Collection $transporteurs
     * @param $iTotalRecords
     * @param $iDisplayStart
     * @param $iDisplayLength
     * @return array
     */
    private function handleResults($transporteurs, $iTotalRecords, $iDisplayStart, $iDisplayLength)
    {
        //filtering
        if ($transporteurs === null) return array();

        if ($this->code_filter != null) {
            $transporteurs = $transporteurs->filter(function ($e) {//filtrage select
                /** @var Profile_transporteur $e */
                return $e->getCode() === $this->code_filter;
            });
        }
        if ($this->matricule_filter != null) {
            $transporteurs = $transporteurs->filter(function ($e) {//filtrage select
                /** @var Profile_transporteur $e */
                return $e->getMatricule() === $this->matricule_filter;
            });
        }

        if ($this->isLivreur_boutique_filter != null) {
            $transporteurs = $transporteurs->filter(function ($e) {//search for occurences in the text
                /** @var Profile_transporteur $e */
                return $e->getLivreurBoutique() instanceof Livreur_boutique;
            });
        }

        $transporteurs = ($transporteurs !== null) ? $transporteurs->toArray() : [];
        //assortment: descending of date -- du plus recent au plus ancient
        usort(
            $transporteurs, function ($e1, $e2) {
            /**
             * @var Profile_transporteur $e1
             * @var Profile_transporteur $e2
             */
            $dt1 = $e1->getDateEnregistrement()->getTimestamp();
            $dt2 = $e2->getDateEnregistrement()->getTimestamp();
            return $dt1 <= $dt2 ? 1 : -1;
        });
        if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
        //paging; slice and preserve keys' order
        $transporteurs = array_slice($transporteurs, $iDisplayStart, $iDisplayLength, true);

        return $transporteurs;
    }

    /**
     * @ApiDoc(
     * resource=true,
     * resourceDescription="Operations on transporteur.",
     * description="Create an object of type transporteur.",
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
     *     "class"="APM\TransportBundle\Form\Profile_transporteurType",
     *     "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\FormTypeParser"
     *      }
     * },
     * views = {"default", "transport" }
     * )
     * @param Request $request
     * @return View | JsonResponse
     *
     * @Post("/new/transporteur")
     */
    public function newAction(Request $request)
    {
        try {
            $this->createSecurity();
            /** @var Profile_transporteur $profile_transporteur */
            $profile_transporteur = TradeFactory::getTradeProvider("transporteur");
            $form = $this->createForm('APM\TransportBundle\Form\Profile_transporteurType', $profile_transporteur);
            $form->remove('utilisateur');
            $data = $request->request->has($form->getName()) ? $request->request->get($form->getName()) : $data[$form->getName()] = array();
            $form->submit($data);
            if (!$form->isValid()) {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $profile_transporteur->setUtilisateur($this->getUser());
            $em = $this->getEM();
            $em->persist($profile_transporteur);
            $em->flush();
            return $this->routeRedirectView("api_transport_show_transporteur", ['id' => $profile_transporteur->getId()], Response::HTTP_CREATED);
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
        $this->denyAccessUnlessGranted('ROLE_TRANSPORTEUR', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        //vérifier si l'utilisateur n'est pas déjà enregistré comme transporteur ou livreur
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $em = $this->getEM();
        $utilisateur = null;
        $utilisateur = $em->getRepository('APMTransportBundle:Profile_transporteur')->findOneBy(['utilisateur' => $user->getId()]);
        if (null !== $utilisateur) throw $this->createAccessDeniedException();
        //----------------------------------------------------------------------------------------
    }

    /**
     * @ApiDoc(
     * resource=true,
     * description="Retrieve the details of an objet of type Transporteur.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * requirements = {
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="transporteur id"}
     * },
     * output={
     *   "class"="APM\TransportBundle\Entity\Profile_transporteur",
     *   "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *    },
     *     "groups"={"owner_transporteur_details", "owner_list"}
     * },
     * statusCodes={
     *     "output" = "A single Object",
     *     200="Returned when successful",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "transport"}
     * )
     * @param Profile_transporteur $profile_transporteur
     * @return JsonResponse
     *
     * @Get("/show/transporteur/{id}")
     */
    public function showAction(Profile_transporteur $profile_transporteur)
    {
        $this->listeAndShowSecurity();
        $data = $this->get('apm_core.data_serialized')->getFormalData($profile_transporteur, ["owner_transporteur_details", "owner_list"]);
        return new JsonResponse($data, 200);
    }

    /**
     * @ApiDoc(
     * resource=true,
     * resourceDescription="Operations on transporteur",
     * description="Update an object of type Transporteur.",
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
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="transporteur Id"}
     * },
     * authentication= true,
     * authenticationRoles= {
     *          "ROLE_TRANSPORTEUR"
     *     },
     * input={
     *     "class"="APM\TransportBundle\Form\Profile_transporteurType",
     *     "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\FormTypeParser"
     *      }
     * },
     * views = {"default", "transport" }
     * )
     * @param Request $request
     * @param Profile_transporteur $profile_transporteur
     * @return View | JsonResponse
     * @Put("/edit/transporteur/{id}")
     */
    public function editAction(Request $request, Profile_transporteur $profile_transporteur)
    {
        try {
            $this->editAndDeleteSecurity($profile_transporteur);
            $form = $this->createForm('APM\TransportBundle\Form\Profile_transporteurType', $profile_transporteur);
            $data = $request->request->has($form->getName()) ? $request->request->get($form->getName()) : $data[$form->getName()] = array();
            $form->submit($data, false);
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
     * @param Profile_transporteur $transporteur
     */
    private function editAndDeleteSecurity($transporteur)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_TRANSPORTEUR', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        //autoriser la modification uniquement qau transporteur autonome de droit exclut tout livreur boutique
        $user = $this->getUser();
        if ($transporteur->getLivreurBoutique() || $user !== $transporteur->getUtilisateur()) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @ApiDoc(
     * resource=true,
     * description="Delete objet of type Transporteur.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * requirements = {
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="transporteur Id"}
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
     * @param Profile_transporteur $profile_transporteur
     * @return View | JsonResponse
     *
     * @Delete("/delete/transporteur/{id}")
     */
    public function deleteAction(Request $request, Profile_transporteur $profile_transporteur)
    {
        try {
            $this->editAndDeleteSecurity($profile_transporteur);
            if (!$request->request->has('exec') || $request->request->get('exec') !== 'go') {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans('impossible de supprimer', [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $em = $this->getEM();
            $em->remove($profile_transporteur);
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
