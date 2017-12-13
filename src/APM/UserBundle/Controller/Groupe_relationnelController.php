<?php

namespace APM\UserBundle\Controller;

use APM\UserBundle\Entity\Groupe_relationnel;
use APM\UserBundle\Entity\Individu_to_groupe;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\UserBundle\Factory\TradeFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Put;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;


/**
 * Groupe_relationnel controller.
 * @RouteResource("groupeRelationnel", pluralize=false)
 */
class Groupe_relationnelController extends Controller
{
    private $dateCreationFrom_filter;
    private $dateCreationTo_filter;
    private $code_filter;
    private $description_filter;
    private $designation_filter;
    private $conversationalGroup_filter;
    private $type_filter;
    private $proprietaire_filter;
    private $boutique_filter;

    /**
     * Liste tous les groupes relationnels crées par l'utilisateur
     * @param Request $request
     * @return JsonResponse
     *
     * @Get("/cget/groupeRelationnels", name="s")
     */
    public function getAction(Request $request)
    {
        $this->listAndShowSecurity();
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $q = $request->get('q');
        $this->dateCreationFrom_filter = $request->request->has('dateCreationFrom_filter') ? $request->request->get('dateCreationFrom_filter') : "";
        $this->dateCreationTo_filter = $request->request->has('dateCreationTo_filter') ? $request->request->get('dateCreationTo_filter') : "";
        $this->code_filter = $request->request->has('code_filter') ? $request->request->get('code_filter') : "";
        $this->description_filter = $request->request->has('description_filter') ? $request->request->get('description_filter') : "";
        $this->designation_filter = $request->request->has('designation_filter') ? $request->request->get('designation_filter') : "";
        $this->conversationalGroup_filter = $request->request->has('conversationalGroup_filter') ? $request->request->get('conversationalGroup_filter') : "";
        $this->type_filter = $request->request->has('type_filter') ? $request->request->get('type_filter') : "";
        $this->proprietaire_filter = $request->request->has('proprietaire_filter') ? $request->request->get('proprietaire_filter') : "";
        $iDisplayLength = $request->request->has('length') ? $request->request->get('length') : -1;
        $iDisplayStart = $request->request->has('start') ? intval($request->request->get('start')) : 0;
        $json = array();
        $json['items'] = array();
        if ($q === "owner" || $q === "all") {
            $groupes = $user->getGroupesProprietaire();
            $iTotalRecords = count($groupes);
            if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
            $groupes = $this->handleResults($groupes, $iTotalRecords, $iDisplayStart, $iDisplayLength);
            $iFilteredRecords = count($groupes);
            $data = $this->get('apm_core.data_serialized')->getFormalData($groupes, array("owner_list"));
            $json['totalRecordsOwner'] = $iTotalRecords;
            $json['filteredRecordsOwner'] = $iFilteredRecords;
            $json['items'] = $data;
        }
        if ($q === "guest" || $q === "all") {
            //----- Ajout des groupes de conversation : groupes auxquels appartient l'utilisateur ---------------------
            $individu_groupes = $user->getIndividuGroupes();
            $groupesConversationnel = new ArrayCollection();;
            if (null !== $individu_groupes) {
                foreach ($individu_groupes as $individu_groupe) {
                    /** @var Groupe_relationnel $groupe_relationnel */
                    $groupe_relationnel = $individu_groupe->getGroupeRelationnel();
                    if ($groupe_relationnel->isConversationalGroup() && $user !== $groupe_relationnel->getProprietaire()) {
                        $groupesConversationnel->add($groupe_relationnel);
                    };
                }
                $iTotalRecords = count($groupesConversationnel);
                if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
                $groupes = $this->handleResults($groupesConversationnel, $iTotalRecords, $iDisplayStart, $iDisplayLength);
                $iFilteredRecords = count($groupes);
                $data = $this->get('apm_core.data_serialized')->getFormalData($groupes, array("owner_list"));
                $json['totalRecordsGuest'] = $iTotalRecords;
                $json['filteredRecordsGuest'] = $iFilteredRecords;
                $json['items'] = $data;
            }
            //---------------------------------------------------------------------------------------------------------
        }
        return new JsonResponse($json, 200);
    }

    private function listAndShowSecurity()
    {
        //-----------------------------------security-------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @param Collection $groupes
     * @param $iTotalRecords
     * @param $iDisplayStart
     * @param $iDisplayLength
     * @return array
     */
    private function handleResults($groupes, $iTotalRecords, $iDisplayStart, $iDisplayLength)
    {
        //filtering
        if ($groupes === null) return array();

        if ($this->code_filter != null) {
            $groupes = $groupes->filter(function ($e) {//filtrage select
                /** @var Groupe_relationnel $e */
                return $e->getCode() === $this->code_filter;
            });
        }
        if ($this->type_filter != null) {
            $groupes = $groupes->filter(function ($e) {//filtrage select
                /** @var Groupe_relationnel $e */
                return $e->getType() === $this->type_filter;
            });
        }
        if ($this->conversationalGroup_filter != null) {
            $groupes = $groupes->filter(function ($e) {//filtrage select
                /** @var Groupe_relationnel $e */
                return $e->getConversationalGroup() === boolval($this->conversationalGroup_filter);
            });
        }
        if ($this->dateCreationFrom_filter != null) {
            $groupes = $groupes->filter(function ($e) {//start date
                /** @var Groupe_relationnel $e */
                $dt1 = (new \DateTime($e->getUpdatedAt()->format('d-m-Y')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateCreationFrom_filter))->getTimestamp();
                return $dt1 - $dt2 >= 0 ? true : false; //start from the given date 'dateFrom_filter'
            });
        }
        if ($this->dateCreationTo_filter != null) {
            $groupes = $groupes->filter(function ($e) {//end date
                /** @var Groupe_relationnel $e */
                $dt = (new \DateTime($e->getUpdatedAt()->format('d-m-Y')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateCreationTo_filter))->getTimestamp();
                return $dt - $dt2 <= 0 ? true : false;// end from at the given date 'dateTo_filter'
            });
        }
        if ($this->boutique_filter != null) {
            $groupes = $groupes->filter(function ($e) {//filter with the begining of the entering word
                /** @var Groupe_relationnel $e */
                $str1 = $e->getBoutique()->getDesignation();
                $str2 = $this->boutique_filter;
                $len = strlen($str2);
                return strncasecmp($str1, $str2, $len) === 0 ? true : false;
            });
        }
        if ($this->designation_filter != null) {
            $groupes = $groupes->filter(function ($e) {//search for occurences in the text
                /** @var Groupe_relationnel $e */
                $subject = $e->getDesignation();
                $pattern = $this->designation_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }
        if ($this->description_filter != null) {
            $groupes = $groupes->filter(function ($e) {//search for occurences in the text
                /** @var Groupe_relationnel $e */
                $subject = $e->getDescription();
                $pattern = $this->description_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }
        $groupes = ($groupes !== null) ? $groupes->toArray() : [];
        //assortment: descending of date -- du plus recent au plus ancient
        usort(
            $groupes, function ($e1, $e2) {
            /**
             * @var Groupe_relationnel $e1
             * @var Groupe_relationnel $e2
             */
            $dt1 = $e1->getUpdatedAt()->getTimestamp();
            $dt2 = $e2->getUpdatedAt()->getTimestamp();
            return $dt1 <= $dt2 ? 1 : -1;
        });
        if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
        //paging; slice and preserve keys' order
        $groupes = array_slice($groupes, $iDisplayStart, $iDisplayLength, true);

        return $groupes;
    }

    /**
     * Creates a new Groupe_relationnel entity.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response | JsonResponse
     *
     * @Post("/new/grouperelationnel")
     */
    public function newAction(Request $request)
    {
        $this->createSecurity();
        /** @var Session $session */
        $session = $request->getSession();
        /** @var Groupe_relationnel $groupe_relationnel */
        $groupe_relationnel = TradeFactory::getTradeProvider("groupe_relationnel");
        $form = $this->createForm('APM\UserBundle\Form\Groupe_relationnelType');
        $form->setData($groupe_relationnel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->createSecurity();
                $em = $this->getDoctrine()->getManager();
                $groupe_relationnel->setProprietaire($this->getUser());
                $em->persist($groupe_relationnel);
                $em->flush();
                if ($request->isXmlHttpRequest()) {
                    $json = array();
                    $json["item"] = array(//prevenir le client
                        "action" => 0,
                    );
                    $session->getFlashBag()->add('success', "<strong> rabais d'offre créée. réf:" . $groupe_relationnel->getCode() . "</strong><br> Opération effectuée avec succès!");
                    return $this->json(json_encode($json), 200);
                }

                if (null !== $groupe_relationnel->getImage()) {
                    $this->get('apm_core.crop_image')->liipImageResolver($groupe_relationnel->getImage());
                    return $this->redirectToRoute('apm_user_groupe-relationnel_show-image', array('id' => $groupe_relationnel->getId()));
                } else {
                    return $this->redirectToRoute('apm_user_groupe-relationnel_show', array('id' => $groupe_relationnel->getId()));
                }
                //---
            } catch (ConstraintViolationException $cve) {
                $session->getFlashBag()->add('danger', "<strong>Echec de l'enregistrement. </strong><br>L'enregistrement a échoué dû à une contrainte de données!");
                return $this->json(json_encode(["item" => null]));
            } catch (RuntimeException $rte) {
                $session->getFlashBag()->add('danger', "<strong>Echec de l'opération.</strong><br>L'enregistrement a échoué. bien vouloir réessayer plutard, svp!");
                return $this->json(json_encode(["item" => null]));
            } catch (AccessDeniedException $ads) {
                $session->getFlashBag()->add('danger', "<strong>Action interdite.</strong><br>Vous n'êtes pas autorisez à effectuer cette opération!");
                return $this->json(json_encode(["item" => null]));
            }
        }
        $session->set('previous_location', $request->getUri());
        $data = array(
            'form' => $form->createView(),
            'url_image' => $this->get('apm_core.packages_maker')->getPackages()->getUrl('/', 'resolve_img'),
            'groupe_relationnel' => $groupe_relationnel,
        );
        return $this->render('APMUserBundle:groupe_relationnel:new.html.twig', $data);
    }


    private function createSecurity()
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        /* ensure that the user is logged in
        */
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @param Request $request
     * @param Groupe_relationnel $groupe_relationnel
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @get("/show-image/grouperelationnel/{id}")
     */
    public function showImageAction(Request $request, Groupe_relationnel $groupe_relationnel)
    {
        $this->listAndShowSecurity();
        $form = $this->createCrobForm($groupe_relationnel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('apm_core.crop_image')->setCropParameters(intval($_POST['x']), intval($_POST['y']), intval($_POST['w']), intval($_POST['h']), $groupe_relationnel->getImage(), $groupe_relationnel);

            return $this->redirectToRoute('apm_user_groupe-relationnel_show', array('id' => $groupe_relationnel->getId()));
        }

        return $this->render('APMUserBundle:groupe_relationnel:image.html.twig', array(
            'groupe_relationnel' => $groupe_relationnel,
            'crop_form' => $form->createView(),
            'url_image' => $this->get('apm_core.packages_maker')->getPackages()->getUrl('/', 'resolve_img'),
        ));
    }

    private
    function createCrobForm(Groupe_relationnel $groupe_relationnel)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_user_groupe-relationnel_show-image', array('id' => $groupe_relationnel->getId())))
            ->setMethod('POST')
            ->getForm();
    }

    /**
     * Finds and displays a Groupe_relationnel entity.
     * @param Groupe_relationnel $groupe_relationnel
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Get("/show/grouperelationnel/{id}")
     */
    public function showAction(Groupe_relationnel $groupe_relationnel)
    {
        $this->listAndShowSecurity();
        $data = $this->get('apm_core.data_serialized')->getFormalData($groupe_relationnel, ["owner_groupeR_details", "owner_list"]);
        return new JsonResponse($data, 200);
    }

    /**
     * Displays a form to edit an existing Groupe_relationnel entity.
     * @param Request $request
     * @param Groupe_relationnel $groupe_relationnel
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Put("/edit/grouperelationnel/{id}")
     */
    public function editAction(Request $request, Groupe_relationnel $groupe_relationnel)
    {
        $this->editAndDeleteSecurity($groupe_relationnel);
        if ($request->isXmlHttpRequest() && $request->getMethod() === "POST") {
            $json = array();
            $json['item'] = array();
            /** @var Session $session */
            $session = $request->getSession();
            $em = $this->getDoctrine()->getManager();
            $property = $request->request->get('name');
            $value = $request->request->get('value');
            switch ($property) {
                case 'designation':
                    $groupe_relationnel->setDesignation($value);
                    break;
                case 'description' :
                    $groupe_relationnel->setDescription($value);
                    break;
                case 'conversationalGroup' :
                    $groupe_relationnel->setConversationalGroup($value);
                    break;
                case 'type' :
                    $groupe_relationnel->setType($value);
                    break;
                default:
                    $session->getFlashBag()->add('info', "<strong> Aucune mis à jour effectuée</strong>");
                    return $this->json(json_encode(["item" => null]), 205);
            }
            $em->flush();
            $session->getFlashBag()->add('success', "Mis à jour propriété : <strong>" . $property . "</strong> réf. groupe_relationnel :" . $groupe_relationnel->getCode() . "<br> Opération effectuée avec succès!");
            return $this->json(json_encode($json), 200);
        }
        $deleteForm = $this->createDeleteForm($groupe_relationnel);
        $editForm = $this->createForm('APM\UserBundle\Form\Groupe_relationnelType', $groupe_relationnel);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->editAndDeleteSecurity($groupe_relationnel);
            $em = $this->getDoctrine()->getManager();
            $em->persist($groupe_relationnel);
            $em->flush();
            //---
            $dist = dirname(__DIR__, 4);
            $file = $dist . '/web/' . $this->getParameter('images_url') . '/' . $groupe_relationnel->getImage();
            if (file_exists($file)) {
                return $this->redirectToRoute('apm_user_groupe-relationnel_show-image', array('id' => $groupe_relationnel->getId()));
            } else {
                return $this->redirectToRoute('apm_user_groupe-relationnel_show', array('id' => $groupe_relationnel->getId()));
            }
            //---
        }

        return $this->render('APMUserBundle:groupe_relationnel:edit.html.twig', array(
            'groupe_relationnel' => $groupe_relationnel,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'url_image' => $this->get('apm_core.packages_maker')->getPackages()->getUrl('/', 'resolve_img'),
        ));
    }

    /**
     * @param Groupe_relationnel $groupe
     *
     */
    private function editAndDeleteSecurity($groupe)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');

        /* ensure that the user is logged in
        *  and that the one is the owner
        */
        $user = $this->getUser();
        if ((!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) || ($groupe->getProprietaire() !== $user)) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------

    }

    /**
     * Creates a form to delete a Groupe_relationnel entity.
     *
     * @param Groupe_relationnel $groupe_relationnel The Groupe_relationnel entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private
    function createDeleteForm(Groupe_relationnel $groupe_relationnel)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_user_groupe-relationnel_delete', array('id' => $groupe_relationnel->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Deletes a Groupe_relationnel entity.
     * @param Request $request
     * @param Groupe_relationnel $groupe_relationnel
     * @return \Symfony\Component\HttpFoundation\RedirectResponse | JsonResponse
     *
     * @Delete("/delete/grouperelationnel/{id}")
     */
    public function deleteAction(Request $request, Groupe_relationnel $groupe_relationnel)
    {
        $this->editAndDeleteSecurity($groupe_relationnel);
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest() && $request->getMethod() === "POST") {
            $em->remove($groupe_relationnel);
            $em->flush();
            $json = array();
            return $this->json($json, 200);
        }
        $form = $this->createDeleteForm($groupe_relationnel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->editAndDeleteSecurity($groupe_relationnel);
            $em->remove($groupe_relationnel);
            $em->flush();
        }

        return $this->redirectToRoute('apm_user_groupe-relationnel_index');
    }

}
