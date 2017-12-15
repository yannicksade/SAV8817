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
     * @param Request $request
     * @return JsonResponse
     *
     * @Get("/cget/transporteurs", name="s")
     */
    public function getAction(Request $request)
    {
        try {
            $this->listeAndShowSecurity();
            $em = $this->getDoctrine()->getManager();
            $transporteurs = $em->getRepository('APMTransportBundle:Profile_transporteur')->findAll();
            $profile_transporteurs = new ArrayCollection($transporteurs);
            $json = array();
            $this->matricule_filter = $request->query->has('matricule_filter') ? $request->query->get('matricule_filter') : "";
            $this->code_filter = $request->query->has('code_filter') ? $request->query->get('code_filter') : "";
            $this->livreur_boutique = $request->query->has('livreur_boutique') ? $request->query->get('livreur_boutique') : "";
            $iDisplayLength = $request->query->has('length') ? $request->query->get('length') : -1;
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
     * Creates a new Profile_transporteur entity.
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
            $form->submit($request->request->all());
            if (!$form->isValid()) {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $profile_transporteur->setUtilisateur($this->getUser());
            $em = $this->getDoctrine()->getManager();
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
        $em = $this->getDoctrine()->getManager();
        $utilisateur = null;
        $utilisateur = $em->getRepository('APMTransportBundle:Profile_transporteur')->findOneBy(['utilisateur' => $user->getId()]);
        if (null !== $utilisateur) throw $this->createAccessDeniedException();
        //----------------------------------------------------------------------------------------
    }

    /**
     * Finds and displays a Profile_transporteur entity.
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
     * @param Request $request
     * @param Profile_transporteur $profile_transporteur
     * @return View | JsonResponse
     * @Post("/edit/transporteur/{id}")
     */
    public function editAction(Request $request, Profile_transporteur $profile_transporteur)
    {
        try {
            $this->editAndDeleteSecurity($profile_transporteur);
            $form = $this->createForm('APM\TransportBundle\Form\Profile_transporteurType', $profile_transporteur);
            $form->submit($request->request->all(), false);
            if (!$form->isValid()) {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->routeRedirectView("api_transport_show_transporteur", ['id' => $profile_transporteur->getId()], Response::HTTP_OK);
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
     * Deletes a Profile_transporteur entity.
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
            $user = $profile_transporteur->getUtilisateur();
            $em = $this->getDoctrine()->getManager();
            $em->remove($profile_transporteur);
            $em->flush();
            return $this->routeRedirectView("api_user_show", [$user->getId()], Response::HTTP_OK);
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
