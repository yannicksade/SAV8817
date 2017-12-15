<?php

namespace APM\MarketingDistribueBundle\Controller;

use APM\MarketingDistribueBundle\Entity\Conseiller;
use APM\MarketingDistribueBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use Doctrine\Common\Collections\ArrayCollection;
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
 * Conseiller controller.
 * @RouteResource("conseiller", pluralize=false)
 */
class ConseillerController extends FOSRestController
{
    private $matricule_filter;
    private $code_filter;
    private $dateFrom_filter;
    private $dateTo_filter;
    private $valeurQuota_filter;
    private $description_filter;
    private $dateCreationReseauFrom_filter;
    private $dateCreationReseauTo_filter;
    private $isConseillerA2;

    /**
     * Liste tous les conseillers
     * @return JsonResponse
     *
     * @Get("/cget/conseiller")
     */
    public function getAction()
    {
        try {
            $this->listAndShowSecurity();
            $em = $this->getDoctrine()->getManager();
            $conseillers = $em->getRepository('APMMarketingDistribueBundle:conseiller')->findAll();
            $json = array();
            $this->matricule_filter = $request->request->has('matricule_filter') ? $request->request->get('matricule_filter') : "";
            $this->code_filter = $request->request->has('code_filter') ? $request->request->get('code_filter') : "";
            $this->dateFrom_filter = $request->request->has('dateFrom_filter') ? $request->request->get('dateFrom_filter') : "";
            $this->dateTo_filter = $request->request->has('dateTo_filter') ? $request->request->get('dateTo_filter') : "";
            $this->valeurQuota_filter = $request->request->has('valeurQuota_filter') ? $request->request->get('valeurQuota_filter') : "";
            $this->description_filter = $request->request->has('description_filter') ? $request->request->get('description_filter') : "";
            $this->dateCreationReseauFrom_filter = $request->request->has('dateCreationReseauFrom_filter') ? $request->request->get('dateCreationReseauFrom_filter') : "";
            $this->dateCreationReseauTo_filter = $request->request->has('dateCreationReseauTo_filter') ? $request->request->get('dateCreationReseauTo_filter') : "";
            $iDisplayLength = $request->request->has('length') ? $request->request->get('length') : -1;
            $iDisplayStart = $request->request->has('start') ? intval($request->request->get('start')) : 0;
            $iTotalRecords = count($conseillers);
            if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
            $_conseillers = new ArrayCollection();
            foreach ($conseillers as $conseiller) {
                $_conseillers->add($conseiller);
            }
            $conseillers = $this->handleResults($_conseillers, $iTotalRecords, $iDisplayStart, $iDisplayLength);
            $iFilteredRecords = count($conseillers);
            $data = $this->get('apm_core.data_serialized')->getFormalData($conseillers, array("others_list"));
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

    private function listAndShowSecurity()
    {
        //-----------------------------------security-------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @param Collection $conseillers
     * @param $iTotalRecords
     * @param $iDisplayStart
     * @param $iDisplayLength
     * @return array
     */
    private function handleResults($conseillers, $iTotalRecords, $iDisplayStart, $iDisplayLength)
    {
        //filtering
        if ($conseillers === null) return array();

        if ($this->code_filter != null) {
            $conseillers = $conseillers->filter(function ($e) {
                /** @var Conseiller $e */
                return $e->getCode() === $this->code_filter;
            });
        }
        if ($this->matricule_filter != null) {
            $conseillers = $conseillers->filter(function ($e) {
                /** @var Conseiller $e */
                return $e->getMatricule() === $this->matricule_filter;
            });
        }
        if ($this->isConseillerA2 != null) {
            $conseillers = $conseillers->filter(function ($e) {
                /** @var Conseiller $e */
                return $e->getConseillerA2() === boolval($this->isConseillerA2);
            });
        }

        if ($this->dateFrom_filter != null) {
            $conseillers = $conseillers->filter(function ($e) {//start date
                /** @var Conseiller $e */
                $dt1 = (new \DateTime($e->getDateEnregistrement()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateFrom_filter))->getTimestamp();
                return $dt1 - $dt2 >= 0 ? true : false; //start from the given date 'dateFrom_filter'
            });
        }
        if ($this->dateTo_filter != null) {
            $conseillers = $conseillers->filter(function ($e) {//end date
                /** @var Conseiller $e */
                $dt = (new \DateTime($e->getDateEnregistrement()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateTo_filter))->getTimestamp();
                return $dt - $dt2 <= 0 ? true : false;// end from at the given date 'dateTo_filter'
            });
        }
        if ($this->dateFrom_filter != null) {
            $conseillers = $conseillers->filter(function ($e) {//start date
                /** @var Conseiller $e */
                $dt1 = (new \DateTime($e->getDateCreationReseau()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateFrom_filter))->getTimestamp();
                return $dt1 - $dt2 >= 0 ? true : false; //start from the given date 'dateFrom_filter'
            });
        }
        if ($this->dateTo_filter != null) {
            $conseillers = $conseillers->filter(function ($e) {//end date
                /** @var Conseiller $e */
                $dt = (new \DateTime($e->getDateCreationReseau()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateTo_filter))->getTimestamp();
                return $dt - $dt2 <= 0 ? true : false;// end from at the given date 'dateTo_filter'
            });
        }
        if ($this->description_filter != null) {
            $conseillers = $conseillers->filter(function ($e) {//search for occurences in the text
                /** @var Conseiller $e */
                $subject = $e->getDescription();
                $pattern = $this->description_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }
        $conseillers = ($conseillers !== null) ? $conseillers->toArray() : [];
        //assortment: descending of date -- du plus recent au plus ancient
        usort(
            $conseillers, function ($e1, $e2) {
            /**
             * @var Conseiller $e1
             * @var Conseiller $e2
             */
            $dt1 = $e1->getDateEnregistrement()->getTimestamp();
            $dt2 = $e2->getDateEnregistrement()->getTimestamp();
            return $dt1 <= $dt2 ? 1 : -1;
        });

        if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
        //paging; slice and preserve keys' order
        $conseillers = array_slice($conseillers, $iDisplayStart, $iDisplayLength, true);

        return $conseillers;
    }

    /**
     * Creates a new Conseiller entity.
     * @param Request $request
     * @return View | JsonResponse
     *
     * @Post("/new")
     */
    public function newAction(Request $request)
    {
        try {
            $this->createSecurity();
            /** @var Conseiller $conseiller */
            $conseiller = TradeFactory::getTradeProvider("conseiller");
            $form = $this->createForm('APM\MarketingDistribueBundle\Form\ConseillerType', $conseiller);
            $form->submit($request->request->all());
            if (!$form->isValid()) {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }

            $conseiller->setUtilisateur($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($conseiller);
            $em->flush();

            return $this->routeRedirectView("api_marketing_show_conseiller", ['id' => $conseiller->getId()], Response::HTTP_CREATED);

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
        // Unable to access the controller unless you have a CONSEILLER role
        $this->denyAccessUnlessGranted('ROLE_CONSEILLER', null, 'Unable to access this page!');
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') || !$user->isConseillerA1()) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * Finds and displays a Conseiller entity.
     * @param Conseiller $conseiller
     * @return JsonResponse
     *
     * @Get("/show/{id}")
     */
    public function showAction(Conseiller $conseiller)
    {
        $this->listAndShowSecurity();
        $data = $this->get('apm_core.data_serialized')->getFormalData($conseiller, ["owner_conseiller_details", "owner_list"]);
        return new JsonResponse($data, 200);
    }

    /**
     * @param Request $request
     * @param Conseiller $conseiller
     * @return View | JsonResponse
     *
     * @Get("/edit/conseiller/{id}")
     */
    public function editAction(Request $request, Conseiller $conseiller)
    {
        try {
            $this->editAndDeleteSecurity($conseiller);
            $form = $this->createForm('APM\MarketingDistribueBundle\Form\ConseillerType', $conseiller);
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

            return $this->routeRedirectView("api_marketing_show_conseiller", ['id' => $conseiller->getId()], Response::HTTP_OK);

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
     * @param Conseiller $conseiller
     */
    private function editAndDeleteSecurity($conseiller)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a CONSEILLER role
        $this->denyAccessUnlessGranted('ROLE_CONSEILLER', null, 'Unable to access this page!');
        $user = $this->getUser();
        if ((!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) ||
            ($conseiller->getUtilisateur() !== $user)
        ) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }


    /**
     * @param Request $request
     * @param Conseiller $conseiller
     * @return View | JsonResponse
     *
     * @Delete("/delete/profile-conseiller/{id}")
     */
    public function deleteAction(Request $request, Conseiller $conseiller)
    {
        try {
            $this->editAndDeleteSecurity($conseiller);
            if (!$request->request->has('exec') || $request->request->get('exec') !== 'go') {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans('impossible de supprimer', [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $user = $conseiller->getUtilisateur();
            $em = $this->getDoctrine()->getManager();
            $em->remove($conseiller);
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
