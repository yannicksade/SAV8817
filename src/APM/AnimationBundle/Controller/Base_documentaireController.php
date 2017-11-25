<?php

namespace APM\AnimationBundle\Controller;

use APM\AnimationBundle\Entity\Base_documentaire;
use APM\AnimationBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
 * Base_documentaire controller.
 * @RouteResource("document")
 */
class Base_documentaireController extends Controller
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
     * @return \Symfony\Component\HttpFoundation\Response | JsonResponse
     *
     * @Get("/documents")
     */
    public function getAction(Request $request)
    {
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $documents = $user->getDocuments();
        if ($request->isXmlHttpRequest()) {
            $json = array();
            $json['items'] = array();
            $this->objet_filter = $request->request->has('objet_filter') ? $request->request->get('objet_filter') : "";
            $this->code_filter = $request->request->has('code_filter') ? $request->request->get('code_filter') : "";
            $this->dateFrom_filter = $request->request->has('dateFrom_filter') ? $request->request->get('dateFrom_filter') : "";
            $this->dateTo_filter = $request->request->has('dateTo_filter') ? $request->request->get('dateTo_filter') : "";
            $this->updatedAt_filter = $request->request->has('updatedAt_filter') ? $request->request->get('updatedAt_filter') : "";
            $this->proprietaire_filter = $request->request->has('proprietaire_filter') ? $request->request->get('proprietaire_filter') : "";

            $iDisplayLength = $request->request->has('length') ? $request->request->get('length') : -1;
            $iDisplayStart = $request->request->has('start') ? intval($request->request->get('start')) : 0;

            $iTotalRecords = count($documents);
            if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
            $documents = $this->handleResults($documents, $iTotalRecords, $iDisplayStart, $iDisplayLength);
            /** @var Base_documentaire $document */
            foreach ($documents as $document) {
                array_push($json['items'], array(
                    'id' => $document->getId(),
                    'code' => $document->getCode(),
                    'objet' => $document->getObjet(),
                    'description' => $document->getDescription()
                ));
            }
            return $this->json(json_encode($json), 200);
        }
        return $this->render('APMAnimationBundle:document:index.html.twig', array(
            'documents' => $documents,
        ));
    }

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

    /*
     * Rename and Download a file
     * @Get("/download/document/{id}")
     */
    public function downloadFileAction(Base_documentaire $document)
    {
        $this->listAndShowSecurity();
        $downloadHandler = $this->get('vich_uploader.download_handler');
        $fileName = $document->getBrochure();
        $fileName = explode('.', $fileName);
        $fileName = $document->getObjet() . '.' . $fileName[1];
        return $downloadHandler->downloadObject($document, $fileField = 'productFile', $objectClass = null, $fileName);
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

    /**
     * Creates a new Base_documentaire entity.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response| JsonResponse
     *
     * @Post("/new/document")
     */
    public function newAction(Request $request)
    {
        $this->createSecurity();
        /** @var Base_documentaire $document */
        $document = TradeFactory::getTradeProvider("base_documentaire");
        $form = $this->createForm('APM\AnimationBundle\Form\Base_documentaireType', $document);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->createSecurity();
            $document->setProprietaire($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($document);
            $em->flush();
            if ($request->isXmlHttpRequest()) {
                $json = array();
                $json['item'] = array();
                return $this->json(json_encode($json), 200);
            }
            return $this->redirectToRoute('apm_animation_base_documentaire_show', array('id' => $document->getId()));
        }

        return $this->render('APMAnimationBundle:document:new.html.twig', array(
            'document' => $document,
            'form' => $form->createView(),
        ));
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
        if ($request->isXmlHttpRequest()) {
            $json = array();
            $json['item'] = array(
                'id' => $document->getId(),
                'code' => $document->getCode(),
                'objet' => $document->getObjet(),
                'date' => $document->getDate()->format("d/m/Y - H:i"),
                'description' => $document->getDescription(),
                'updatedAt' => $document->getUpdatedAt()->format("d/m/Y - H:i"),
                'proprietaire' => $document->getProprietaire()->getId(),
                'brochure' => $document->getBrochure(),
            );
            return $this->json(json_encode($json), 200);
        }
        $deleteForm = $this->createDeleteForm($document);
        return $this->render('APMAnimationBundle:document:show.html.twig', array(
            'document' => $document,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Base_documentaire entity.
     *
     * @param Base_documentaire $document The Base_documentaire entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Base_documentaire $document)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_animation_base_documentaire_delete', array('id' => $document->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Base_documentaire entity.
     * @param Request $request
     * @param Base_documentaire $document
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Put("/edit/document/{id}")
     */
    public function editAction(Request $request, Base_documentaire $document)
    {
        $this->editAndDeleteSecurity($document);

        $deleteForm = $this->createDeleteForm($document);
        $editForm = $this->createForm('APM\AnimationBundle\Form\Base_documentaireType', $document);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()
            || $request->isXmlHttpRequest() && $request->isMethod('POST')) {
            $this->editAndDeleteSecurity($document);
            $em = $this->getDoctrine()->getManager();
            try {
                if ($request->isXmlHttpRequest()) {
                    $json = array();
                    $json['item'] = array();
                    $property = $request->request->get('name');
                    $value = $request->request->get('value');
                    switch ($property) {
                        case 'objet':
                            $document->setObjet($value);
                            break;
                        case 'brochure':
                            $document->setBrochure($value);
                            break;
                        case 'description':
                            $document->setDescription($value);
                            break;
                        default:
                            $session->getFlashBag()->add('info', "<strong> Aucune mise à jour effectuée </strong>");
                            return $this->json(json_encode(["item" => null]), 205);
                    }
                    $em->flush();
                    $session->getFlashBag()->add('success', "Modification du conseiller boutique : <strong>" . $property . "</strong> <br> Opération effectuée avec succès!");
                    return $this->json(json_encode($json), 200);
                }
                $em->flush();
                return $this->redirectToRoute('apm_animation_base_documentaire_show', array('id' => $document->getId()));
            } catch (ConstraintViolationException $cve) {
                $session->getFlashBag()->add('danger', "<strong>Echec de l'enregistrement. </strong><br>L'enregistrement a échoué dû à une contrainte de données!");
                return $this->json(json_encode(["item" => null]));
            } catch (AccessDeniedException $ads) {
                $session->getFlashBag()->add('danger', "<strong>Action interdite.</strong><br>Vous n'êtes pas autorisez à effectuer cette opération!");
                return $this->json(json_encode(["item" => null]));
            }
        }
        return $this->render('APMAnimationBundle:document:edit.html.twig', array(
            'document' => $document,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
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
     * Deletes a Base_documentaire entity.
     * @param Request $request
     * @param Base_documentaire $document
     * @return \Symfony\Component\HttpFoundation\RedirectResponse | JsonResponse
     *
     * @Delete("/delete/document/{id}")
     */
    public function deleteAction(Request $request, Base_documentaire $document)
    {
        $this->editAndDeleteSecurity($document);
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {
            $em->remove($document);
            $em->flush();
            $json = array();
            $json['item'] = array();
            return $this->json(json_encode($json), 200);
        }
        $form = $this->createDeleteForm($document);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->editAndDeleteSecurity($document);
            $em->remove($document);
            $em->flush();
        }

        return $this->redirectToRoute('apm_animation_base_documentaire_index');
    }

}
