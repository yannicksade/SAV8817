<?php

namespace APM\AnimationBundle\Controller;

use APM\AnimationBundle\Entity\Base_documentaire;
use APM\AnimationBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
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
 * Base_documentaire controller.
 * @RouteResource("document")
 */
class Base_documentaireController extends FOSRestController
{
    private $objet_filter;
    private $code_filter;
    private $dateFrom_filter;
    private $dateTo_filter;
    private $updatedAt_filter;
    private $description_filter;
    private $proprietaire_filter;

    /**
     *Liste les documents de l'utilisateur
     * @param Request $request
     * @return JsonResponse
     *
     * @Get("/cget/documents", name="s")
     */
    public function getAction(Request $request)
    {
        try {
            $this->listAndShowSecurity();
            /** @var Utilisateur_avm $user */
            $user = $this->getUser();
            $documents = $user->getDocuments();
            $json = array();
            $this->objet_filter = $request->query->has('objet_filter') ? $request->query->get('objet_filter') : "";
            $this->code_filter = $request->query->has('code_filter') ? $request->query->get('code_filter') : "";
            $this->dateFrom_filter = $request->query->has('dateFrom_filter') ? $request->query->get('dateFrom_filter') : "";
            $this->dateTo_filter = $request->query->has('dateTo_filter') ? $request->query->get('dateTo_filter') : "";
            $this->updatedAt_filter = $request->query->has('updatedAt_filter') ? $request->query->get('updatedAt_filter') : "";
            $this->proprietaire_filter = $request->query->has('proprietaire_filter') ? $request->query->get('proprietaire_filter') : "";
            $iDisplayLength = $request->query->has('length') ? $request->query->get('length') : -1;
            $iDisplayStart = $request->query->has('start') ? intval($request->query->get('start')) : 0;
            $iTotalRecords = count($documents);
            if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
            $documents = $this->handleResults($documents, $iTotalRecords, $iDisplayStart, $iDisplayLength);
            $iFilteredRecords = count($documents);
            $data = $this->get('apm_core.data_serialized')->getFormalData($documents, array("owner_list"));
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
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /*
     * Rename and Download a file
     * @Get("/download/document/{id}")
     */

    /**
     * @param Collection $documents
     * @param $iTotalRecords
     * @param $iDisplayStart
     * @param $iDisplayLength
     * @return array
     */
    private function handleResults($documents, $iTotalRecords, $iDisplayStart, $iDisplayLength)
    {
        //filtering
        if ($documents === null) return array();

        if ($this->code_filter != null) {
            $documents = $documents->filter(function ($e) {//filtrage select
                /** @var Base_documentaire $e */
                return $e->getCode() === $this->code_filter;
            });
        }
        if ($this->proprietaire_filter != null) {
            $documents = $documents->filter(function ($e) {//filtrage select
                /** @var Base_documentaire $e */
                return $e->getProprietaire()->getCode() === $this->proprietaire_filter;
            });
        }
        if ($this->dateFrom_filter != null) {
            $documents = $documents->filter(function ($e) {//start date
                /** @var Base_documentaire $e */
                $dt1 = (new \DateTime($e->getDate()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateFrom_filter))->getTimestamp();
                return $dt1 - $dt2 >= 0 ? true : false; //start from the given date 'dateFrom_filter'
            });
        }
        if ($this->dateTo_filter != null) {
            $documents = $documents->filter(function ($e) {//end date
                /** @var Base_documentaire $e */
                $dt = (new \DateTime($e->getDate()->format('d-m-Y H:i')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateTo_filter))->getTimestamp();
                return $dt - $dt2 <= 0 ? true : false;// end from at the given date 'dateTo_filter'
            });
        }

        if ($this->objet_filter != null) {
            $documents = $documents->filter(function ($e) {//search for occurences in the text
                /** @var Base_documentaire $e */
                $subject = $e->getObjet();
                $pattern = $this->objet_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }
        if ($this->description_filter != null) {
            $documents = $documents->filter(function ($e) {//search for occurences in the text
                /** @var Base_documentaire $e */
                $subject = $e->getDescription();
                $pattern = $this->description_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }

        $documents = ($documents !== null) ? $documents->toArray() : [];
        //assortment: descending of date -- du plus recent au plus ancient
        usort(
            $documents, function ($e1, $e2) {
            /**
             * @var Base_documentaire $e1
             * @var Base_documentaire $e2
             */
            $dt1 = $e1->getUpdatedAt()->getTimestamp();
            $dt2 = $e2->getUpdatedAt()->getTimestamp();
            return $dt1 <= $dt2 ? 1 : -1;
        });

        if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
        //paging; slice and preserve keys' order
        $documents = array_slice($documents, $iDisplayStart, $iDisplayLength, true);

        return $documents;
    }

    public function downloadFileAction(Base_documentaire $document)
    {
        $this->listAndShowSecurity();
        $downloadHandler = $this->get('vich_uploader.download_handler');
        $fileName = $document->getBrochure();
        $fileName = explode('.', $fileName);
        $fileName = $document->getObjet() . '.' . $fileName[1];
        return $downloadHandler->downloadObject($document, $fileField = 'productFile', $objectClass = null, $fileName);
    }

    /**
     * Creates a new Base_documentaire entity.
     * @param Request $request
     * @return View| JsonResponse
     *
     * @Post("/new/document")
     */
    public function newAction(Request $request)
    {
        try {
            $this->createSecurity();
            /** @var Base_documentaire $document */
            $document = TradeFactory::getTradeProvider("base_documentaire");
            $form = $this->createForm('APM\AnimationBundle\Form\Base_documentaireType', $document);
            $form->submit($request->request->all());
            if (!$form->isValid()) {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans($form->getErrors(true, false), [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }
            $document->setProprietaire($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($document);
            $em->flush();

            return $this->routeRedirectView("api_documentation_show_document ", ['id' => $document->getId()], Response::HTTP_CREATED);

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
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * Finds and displays a Base_documentaire entity.
     * @param Base_documentaire $document
     * @return \Symfony\Component\HttpFoundation\Response | JsonResponse
     *
     * @Get("/show/document/{id}")
     */
    public function showAction(Base_documentaire $document)
    {
        $this->listAndShowSecurity();
        $data = $this->get('apm_core.data_serialized')->getFormalData($document, ["owner_document_details", "owner_list"]);
        return new JsonResponse($data, 200);
    }

    /**
     * Displays a form to edit an existing Base_documentaire entity.
     * @param Request $request
     * @param Base_documentaire $document
     * @return View | JsonResponse
     *
     * @Put("/edit/document/{id}")
     */
    public function editAction(Request $request, Base_documentaire $document)
    {
        try {
            $this->editAndDeleteSecurity($document);
            $form = $this->createForm('APM\AnimationBundle\Form\Base_documentaireType', $document);
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

            return $this->routeRedirectView("api_documentation_show_document", ["id" => $document->getId()], Response::HTTP_OK);

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
     * @param Base_documentaire $document
     */
    private function editAndDeleteSecurity($document)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if ((!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))) {
            throw $this->createAccessDeniedException();
        }
        $user = $this->getUser();
        if ($document->getProprietaire() !== $user) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @param Request $request
     * @param Base_documentaire $document
     * @return View | JsonResponse
     *
     * @Delete("/delete/document/{id}")
     */
    public function deleteAction(Request $request, Base_documentaire $document)
    {
        try {
            $this->editAndDeleteSecurity($document);
            if (!$request->request->has('exec') || $request->request->get('exec') !== 'go') {
                return new JsonResponse([
                    "status" => 400,
                    "message" => $this->get('translator')->trans('impossible de supprimer', [], 'FOSUserBundle')
                ], Response::HTTP_BAD_REQUEST);
            }

            $em = $this->getDoctrine()->getManager();
            $em->remove($document);
            $em->flush();

            return $this->routeRedirectView("api_documentation_get_documents", [], Response::HTTP_OK);

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
