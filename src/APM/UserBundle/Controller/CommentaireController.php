<?php

namespace APM\UserBundle\Controller;

use APM\UserBundle\Entity\Commentaire;
use APM\UserBundle\Factory\TradeFactory;
use APM\VenteBundle\Entity\Offre;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Put;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Commentaire controller.
 * de l'offre peut les publier
 *
 * @RouteResource("commentaire", pluralize=false)
 */
class CommentaireController extends FOSRestController
{
    private $contenu_filter;
    private $dateLimiteFrom_filter;
    private $dateLimiteTo_filter;
    private $publiable_filter;
    private $utilisateur_filter;
    private $evaluationMin_filter;
    private $evaluationMax_filter;


    /**
     * @ApiDoc(
     * resource=true,
     * description="Retrieve list of comments.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * filters={
     *      {"name"="contenu_filter", "dataType"="string"},
     *      {"name"="dateLimiteFrom_filter", "dataType"="dateTime", "pattern"="19-12-2017|ASC"},
     *      {"name"="dateLimiteTo_filter", "dataType"="dateTime", "pattern"="19-12-2017|DESC"},
     *      {"name"="description_filter", "dataType"="string"},
     *      {"name"="publiable_filter", "dataType"="boolean"},
     *      {"name"="utilisateur_filter", "dataType"="string"},
     *      {"name"="length_filter", "dataType"="integer", "requirement"="\d+"},
     *      {"name"="start_filter", "dataType"="integer", "requirement"="\d+"},
     *  },
     *  requirements = {
     *      {"name"="id", "required"=true, "requirement"="\d+", "dataType"="integer", "description"="offre_id"}
     *  },
     * output={
     *   "class"="APM\UserBundle\Entity\Commentaire",
     *   "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *    },
     *     "groups"={"owner_list"}
     * },
     * statusCodes={
     *     "output" = "A single or a collection of comments",
     *     200="Returned when successful",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "user"}
     * )
     * @param Request $request
     * @param Offre $offre
     * @return JsonResponse
     *
     * @Get("/cget/commentaires/offre/{id}", name="s_offre")
     */
    public function getAction(Request $request, Offre $offre)
    {
        try {
            $this->listAndShowSecurity();
            $vendeur = $offre->getVendeur();
            $boutique = $offre->getBoutique();
            $user = $this->getUser();
            $gerant = null;
            $proprietaire = null;
            $commentaires = null;
            if (null !== $boutique) {
                $gerant = $boutique->getGerant();
                $proprietaire = $boutique->getProprietaire();
            }
            $comments = $offre->getCommentaires();
            $commentaires = $comments;
            if ($user !== $vendeur && $user !== $gerant && $user !== $proprietaire) {
                $commentaires = null;
                /** @var Commentaire $commentaire */
                foreach ($comments as $commentaire) { //presenter uniquement les commentaires publiés au publique
                    if ($commentaire->isPubliable() || $commentaire->getUtilisateur() === $user) {
                        $commentaires [] = $commentaire;
                    }
                }
            }

            $this->contenu_filter = $request->query->has('contenu_filter') ? $request->query->get('contenu_filter') : "";
            $this->dateLimiteFrom_filter = $request->query->has('dateLimiteFrom_filter') ? $request->query->get('dateLimiteFrom_filter') : "";
            $this->dateLimiteTo_filter = $request->query->has('dateLimiteTo_filter') ? $request->query->get('dateLimiteTo_filter') : "";
            $this->publiable_filter = $request->query->has('publiable_filter') ? $request->query->get('publiable_filter') : "";
            $this->utilisateur_filter = $request->query->has('utilisateur_filter') ? $request->query->get('utilisateur_filter') : "";
            $iDisplayLength = $request->query->has('length') ? $request->query->get('length') : -1;
            $iDisplayStart = $request->query->has('start') ? intval($request->query->get('start')) : 0;
            $json = array();

            $iTotalRecords = count($commentaires);
            if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
            $commentaires = $this->handleResults($commentaires, $iTotalRecords, $iDisplayStart, $iDisplayLength);
            //filtre
            $iFilteredRecords = count($commentaires);
            $data = $this->get('apm_core.data_serialized')->getFormalData($commentaires, array("owner_list"));
            $json['totalRecords'] = $iTotalRecords;
            $json['filteredRecords'] = $iFilteredRecords;
            $json['items'] = $data;

            return new JsonResponse($json, 200);

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
     *
     * @internal param bool $access
     */
    private function listAndShowSecurity()
    {
        //-----------------------------------security-------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @param $iTotalRecords
     * @param $iDisplayStart
     * @param $iDisplayLength
     * @return array
     * @param Collection $commentaires
     */
    private function handleResults($commentaires, $iTotalRecords, $iDisplayStart, $iDisplayLength)
    {
        //filtering
        if ($commentaires === null) return array();
        if ($this->evaluationMin_filter != null) {
            $commentaires = $commentaires->filter(function ($e) {//filtrage select
                /** @var Commentaire $e */
                return $e->getEvaluation() >= intval($this->evaluationMin_filter);
            });
        }
        if ($this->evaluationMax_filter != null) {
            $commentaires = $commentaires->filter(function ($e) {//filtrage select
                /** @var Commentaire $e */
                return $e->getEvaluation() <= intval($this->evaluationMax_filter);
            });
        }
        if ($this->dateLimiteFrom_filter != null) {
            $commentaires = $commentaires->filter(function ($e) {//start date
                /** @var Commentaire $e */
                $dt1 = (new \DateTime($e->getDate()->format('d-m-Y')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateLimiteFrom_filter))->getTimestamp();
                return $dt1 - $dt2 >= 0 ? true : false; //start from the given date 'dateFrom_filter'
            });
        }
        if ($this->dateLimiteTo_filter != null) {
            $commentaires = $commentaires->filter(function ($e) {//end date
                /** @var Commentaire $e */
                $dt = (new \DateTime($e->getDate()->format('d-m-Y')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateLimiteTo_filter))->getTimestamp();
                return $dt - $dt2 <= 0 ? true : false;// end from at the given date 'dateTo_filter'
            });
        }


        if ($this->contenu_filter != null) {
            $commentaires = $commentaires->filter(function ($e) {//search for occurences in the text
                /** @var Commentaire $e */
                $subject = $e->getContenu();
                $pattern = $this->contenu_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }

        $commentaires = ($commentaires !== null) ? $commentaires->toArray() : [];
        //assortment: descending of date -- du plus recent au plus ancient
        usort(
            $commentaires, function ($e1, $e2) {
            /**
             * @var Commentaire $e1
             * @var Commentaire $e2
             */
            $dt1 = $e1->getDate()->getTimestamp();
            $dt2 = $e2->getDate()->getTimestamp();
            return $dt1 <= $dt2 ? 1 : -1;
        });
        if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
        //paging; slice and preserve keys' order
        $commentaires = array_slice($commentaires, $iDisplayStart, $iDisplayLength, true);

        return $commentaires;
    }

    /**
     * @ApiDoc(
     * resource=true,
     * resourceDescription="Operations on Commentaire.",
     * description="Create an object of type Commentaire.",
     * statusCodes={
     *         201="Returned when successful",
     *         400="Returned when the data are not valid or an unknown error occurred",
     *         403="Returned when the user is not authorized to carry on the action",
     *         404="Returned when the entity is not found",
     * },
     * headers={
     *      { "name"="Authorization",  "required"=true, "description"="Authorization token"}
     * },
     * input={
     *    "class"="APM\UserBundle\Entity\Commentaire",
     *     "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\ValidationParser"
     *      },
     *    "name" = "Commentaire",
     * },
     * views = {"default", "user" }
     * )
     * @param Request $request
     * @param Offre $offre
     * @return View | JsonResponse
     *
     * @Post("/new/communication/offre/{id}", name="_offre")
     */
    public function newAction(Request $request, Offre $offre)
    {
        try {
            $this->createSecurity($offre);
            /** @var Session $session */
            $session = $request->getSession();
            /** @var Commentaire $commentaire */
            $commentaire = TradeFactory::getTradeProvider("commentaire");
            $form = $this->createForm('APM\UserBundle\Form\CommentaireType', $commentaire);
            $form->submit($request->request->all());
            if (!$form->isValid()) {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $this->createSecurity($offre);
            $commentaire->setUtilisateur($this->getUser());
            $commentaire->setOffre($offre);
            $em = $this->getDoctrine()->getManager();
            $em->persist($commentaire);
            $em->flush();
            return $this->routeRedirectView("api_user_show_commentaire", ['id' => $commentaire->getId()], Response::HTTP_CREATED);
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
     */
    private function createSecurity($offre)
    {
        //---------------------------------security-----------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        if (!$offre->getPubliable()) throw $this->createAccessDeniedException();
        //----------------------------------------------------------------------------------------
    }

    /**
     * @ApiDoc(
     * resource=true,
     * description="Retrieve the details of an objet of type Commentaire.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * requirements = {
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="commentaire id"}
     * },
     * output={
     *   "class"="APM\UserBundle\Entity\Commentaire",
     *   "parsers" = {
     *      "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *    },
     *     "groups"={"owner_commentaire_details", "owner_list"}
     * },
     * statusCodes={
     *     "output" = "A single Object",
     *     200="Returned when successful",
     *     403="Returned when the user is not authorized to perform the action",
     *     404="Returned when the specified resource is not found",
     * },
     *     views={"default", "user"}
     * )
     * @param Commentaire $commentaire
     * @return JsonResponse
     *
     * @Get("/show/commentaire/{id}")
     */
    public function showAction(Commentaire $commentaire)
    {
        $this->listAndShowSecurity();
        $data = $this->get('apm_core.data_serialized')->getFormalData($commentaire, ["owner_commentaire_details", "owner_list"]);
        return new JsonResponse($data, 200);
    }

    /**
     * @ApiDoc(
     * resource=true,
     * resourceDescription="Operations on Commentaire",
     * description="Update an object of type Commentaire.",
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
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="commentaire Id"}
     * },
     * input={
     *    "class"="APM\UserBundle\Entity\Commentaire",
     *     "parsers" = {
     *          "Nelmio\ApiDocBundle\Parser\ValidationParser"
     *      },
     *    "name" = "User",
     * },
     *
     * views = {"default", "user" }
     * )
     * @param Request $request
     * @param Commentaire $commentaire
     * @return View | JsonResponse
     *
     * @Put("/edit/commentaire/{id}")
     */
    public function editAction(Request $request, Commentaire $commentaire)
    {
        try {
            $this->editAndDeleteSecurity($commentaire);
            $form = $this->createForm('APM\UserBundle\Form\CommentaireType', $commentaire);
            $form->submit($request->request->all(), false);
            if (!$form->isValid()) {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->routeRedirectView("api_user_show_commentaire", ['id' => $commentaire->getId()], Response::HTTP_OK);
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
     * @param Commentaire $commentaire
     */
    private function editAndDeleteSecurity($commentaire)
    {
        //---------------------------------security-----------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        $user = $this->getUser();
        if ((!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) || ($commentaire->getUtilisateur() !== $user)) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------

    }


    /**
     * @ApiDoc(
     * resource=true,
     * description="Delete objet of type Commentaire.",
     * headers={
     *      { "name"="Authorization", "required"="true", "description"="Authorization token"},
     * },
     * requirements = {
     *      {"name"="id", "dataType"="integer", "required"=true, "requirement"="\d+", "description"="commentaire Id"}
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
     *     views={"default", "user"}
     * )
     * @param Request $request
     * @param Commentaire $commentaire
     * @return View | JsonResponse
     *
     * @Delete("/delete/commentaire/{id}")
     */
    public function deleteAction(Request $request, Commentaire $commentaire)
    {
        try {
            $this->editAndDeleteSecurity($commentaire);
            if (!$request->request->has('exec') || $request->request->get('exec') !== 'go') {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans('impossible de supprimer', [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $em = $this->getDoctrine()->getManager();
            $em->remove($commentaire);
            $em->flush();
            return $this->routeRedirectView("api_user_get_commentaires", [], Response::HTTP_OK);
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
