<?php

namespace APM\UserBundle\Controller;

use APM\UserBundle\Entity\Groupe_relationnel;
use APM\UserBundle\Entity\Individu_to_groupe;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\UserBundle\Factory\TradeFactory;
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
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Put;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


/**
 * Individu_to_groupe controller.
 * @RouteResource("individu-group", pluralize=false)
 */
class Individu_to_groupeController extends FOSRestController
{
    private $dateCreationFrom_filter;
    private $dateCreationTo_filter;
    private $propriete_filter;
    private $description_filter;
    private $utilisateur_filter;

    /**
     * @ParamConverter("utilisateur_avm", options={"mapping": {"user_id":"id"}})
     * @param Request $request
     * @param Groupe_relationnel $groupe_relationnel
     * @param Utilisateur_avm $utilisateur_avm
     * @return JsonResponse
     * @Get("/cget/individus/group/{id}", name="s_group")
     * @Get("/cget/groups/user/{user_id}", name="s_user")
     */
    public function getAction(Request $request, Groupe_relationnel $groupe_relationnel = null, Utilisateur_avm $utilisateur_avm = null)
    {
        try {
            if (null !== $groupe_relationnel) {
                $this->listeAndShowSecurity($groupe_relationnel);
                $individu_to_groupes = $groupe_relationnel->getGroupeIndividus();
            } else {
                $this->listeAndShowSecurity(null, $utilisateur_avm);
                $individu_to_groupes = $utilisateur_avm->getIndividuGroupes();
            }
            $this->dateCreationFrom_filter = $request->request->has('dateCreationFrom_filter') ? $request->request->get('dateCreationFrom_filter') : "";
            $this->dateCreationTo_filter = $request->request->has('dateCreationTo_filter') ? $request->request->get('dateCreationTo_filter') : "";
            $this->propriete_filter = $request->request->has('propriete_filter') ? $request->request->get('propriete_filter') : "";
            $this->description_filter = $request->request->has('description_filter') ? $request->request->get('description_filter') : "";
            $this->utilisateur_filter = $request->request->has('utilisateur_filter') ? $request->request->get('utilisateur_filter') : "";
            $iDisplayLength = $request->request->has('length') ? $request->request->get('length') : -1;
            $iDisplayStart = $request->request->has('start') ? intval($request->request->get('start')) : 0;
            $json = array();
            $iTotalRecords = count($individu_to_groupes);
            if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
            $individu_to_groupes = $this->handleResults($individu_to_groupes, $iTotalRecords, $iDisplayStart, $iDisplayLength);
            $iFilteredRecords = count($individu_to_groupes);
            $data = $this->get('apm_core.data_serialized')->getFormalData($individu_to_groupes, array("owner_list"));
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
     * @param Groupe_relationnel $groupe
     * @param $utilisateur
     */
    private function listeAndShowSecurity($groupe, $utilisateur = null)
    {
        //---------------------------------security-----------------------------------------------
        // Liste tous les groupes auxquels l'utilisateur appartient
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw $this->createAccessDeniedException();
        }
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        if (null !== $utilisateur) {
            if ($user !== $utilisateur) {
                throw $this->createAccessDeniedException();
            }
        }
        if (null !== $groupe) {
            $isGroupMember = false;
            $groupe_individus = $groupe->getGroupeIndividus();
            /** @var Individu_to_groupe $groupe_individu */
            foreach ($groupe_individus as $groupe_individu) {
                if ($groupe_individu->getIndividu() === $user) $isGroupMember = true;
            }

            if ($user !== $groupe->getProprietaire() && !$isGroupMember) {
                throw $this->createAccessDeniedException();
            }
        }
        //-----------------------------------------------------------------------------------------
    }
    //liste les offres d'une individu_to_groupe de produit

    /**
     * @param Collection $individu_to_groupes
     * @param $iTotalRecords
     * @param $iDisplayStart
     * @param $iDisplayLength
     * @return array
     */
    private function handleResults($individu_to_groupes, $iTotalRecords, $iDisplayStart, $iDisplayLength)
    {
        //filtering
        if ($individu_to_groupes === null) return array();

        if ($this->propriete_filter != null) {
            $individu_to_groupes = $individu_to_groupes->filter(function ($e) {//filtrage select
                /** @var Individu_to_groupe $e */
                return $e->getPropriete() === $this->propriete_filter;
            });
        }

        if ($this->dateCreationFrom_filter != null) {
            $individu_to_groupes = $individu_to_groupes->filter(function ($e) {//start date
                /** @var Individu_to_groupe $e */
                $dt1 = (new \DateTime($e->getDateInsertion()->format('d-m-Y')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateCreationFrom_filter))->getTimestamp();
                return $dt1 - $dt2 >= 0 ? true : false; //start from the given date 'dateFrom_filter'
            });
        }
        if ($this->dateCreationTo_filter != null) {
            $individu_to_groupes = $individu_to_groupes->filter(function ($e) {//end date
                /** @var Individu_to_groupe $e */
                $dt = (new \DateTime($e->getDateInsertion()->format('d-m-Y')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateCreationTo_filter))->getTimestamp();
                return $dt - $dt2 <= 0 ? true : false;// end from at the given date 'dateTo_filter'
            });
        }

        if ($this->utilisateur_filter != null) {
            $individu_to_groupes = $individu_to_groupes->filter(function ($e) {//search for occurences in the text
                /** @var Individu_to_groupe $e */
                $subject = $e->getIndividu()->getUsername();
                $pattern = $this->utilisateur_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }
        $individu_to_groupes = ($individu_to_groupes !== null) ? $individu_to_groupes->toArray() : [];
        //assortment: descending of date -- du plus recent au plus ancient
        usort(
            $individu_to_groupes, function ($e1, $e2) {
            /**
             * @var Individu_to_groupe $e1
             * @var Individu_to_groupe $e2
             */
            $dt1 = $e1->getDateInsertion()->getTimestamp();
            $dt2 = $e2->getDateInsertion()->getTimestamp();
            return $dt1 <= $dt2 ? 1 : -1;
        });
        if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
        //paging; slice and preserve keys' order
        $individu_to_groupes = array_slice($individu_to_groupes, $iDisplayStart, $iDisplayLength, true);

        return $individu_to_groupes;
    }

    /**
     *  Affecter l'utilisateur à un groupe
     * @param Request $request
     * @param Groupe_relationnel $groupe_relationnel
     * @return View | JsonResponse
     *
     * @Get("/new/relation/group/{id}", name="_group")
     */
    public function newAction(Request $request, Groupe_relationnel $groupe_relationnel)
    {
        try {
            $this->createSecurity($groupe_relationnel);
            /** @var Individu_to_groupe $individu_to_groupe */
            $individu_to_groupe = TradeFactory::getTradeProvider("individu_to_groupe");
            $form = $this->createForm('APM\UserBundle\Form\Individu_to_groupeType', $individu_to_groupe);
            $form->submit($request->request->all());
            if (!$form->isValid()) {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $this->createSecurity($groupe_relationnel, $individu_to_groupe->getIndividu());
            $individu_to_groupe->setGroupeRelationnel($groupe_relationnel);
            $em = $this->getDoctrine()->getManager();
            $em->persist($individu_to_groupe);
            return $this->routeRedirectView("api_user_show_individu-group", ['id' => $individu_to_groupe->getId()], Response::HTTP_CREATED);
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
     * @param Groupe_relationnel $groupe
     * @param Utilisateur_avm|null $individu
     */
    private function createSecurity($groupe = null, $individu = null)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        if ($groupe) {//se rassurer que le groupe relationnel appartient bien à l'utilisateur
            $user = $this->getUser();
            if ($individu) { //Evite la duplication de personne dans un meme groupe
                $oldIndividu = null;
                $em = $this->getDoctrine()->getManager();
                /** @var Individu_to_groupe $oldIndividu */
                $oldIndividu = $em->getRepository('APMUserBundle:Individu_to_groupe')
                    ->findOneBy(['individu' => $individu]);
                if (null !== $oldIndividu) {
                    $oldGroupe = $oldIndividu->getGroupeRelationnel();
                    if ($user !== $groupe->getProprietaire() || $groupe === $oldGroupe) {
                        throw $this->createAccessDeniedException();
                    }
                }
            }
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * Finds and displays a Individu_to_groupe entity.
     * @param Individu_to_groupe $individu_to_groupe
     * @return JsonResponse
     *
     * @Get("/show/relation/{id}")
     */
    public function showAction(Individu_to_groupe $individu_to_groupe)
    {
        $this->listeAndShowSecurity($individu_to_groupe->getGroupeRelationnel());
        $data = $this->get('apm_core.data_serialized')->getFormalData($individu_to_groupe, ["owner_individuToG", "owner_list"]);
        return new JsonResponse($data, 200);
    }

    /**
     * @param Request $request
     * @param Individu_to_groupe $individu_to_groupe
     * @return View | JsonResponse
     *
     * @Put("/edit/relation/{id}")
     */
    public function editAction(Request $request, Individu_to_groupe $individu_to_groupe)
    {
        try {
            $this->editAndDeleteSecurity($individu_to_groupe);
            $form = $this->createForm('APM\UserBundle\Form\Individu_to_groupeType', $individu_to_groupe);
            $form->submit($request->request->all(), false);
            if (!$form->isValid()) {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->routeRedirectView("api_user_show_individu-group", ['id' => $individu_to_groupe->getId()], Response::HTTP_OK);
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
     * L'utilisateur doit être propriétaire du groupe
     * @param Individu_to_groupe $individu_groupe
     */
    private function editAndDeleteSecurity($individu_groupe)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        $user = $this->getUser();

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') || $individu_groupe->getGroupeRelationnel()->getProprietaire() !== $user) {
            throw $this->createAccessDeniedException();
        }
    }


    /**
     * @param Request $request
     * @param Individu_to_groupe $individu_to_groupe
     * @return View | JsonResponse
     *
     * @Delete("/delete/relation/{id}")
     */
    public function deleteAction(Request $request, Individu_to_groupe $individu_to_groupe)
    {
        try {
            $this->editAndDeleteSecurity($individu_to_groupe);
            if (!$request->request->has('exec') || $request->request->get('exec') !== 'go') {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans('impossible de supprimer', [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $individu = $individu_to_groupe->getIndividu();
            $em = $this->getDoctrine()->getManager();
            $em->remove($individu_to_groupe);
            $em->flush();
            return $this->routeRedirectView("api_user_get_individu-groups_user", ["user_id" => $individu->getId()], Response::HTTP_OK);
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
