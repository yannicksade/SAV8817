<?php

namespace APM\UserBundle\Controller;

use APM\UserBundle\Entity\Groupe_relationnel;
use APM\UserBundle\Entity\Individu_to_groupe;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\UserBundle\Factory\TradeFactory;
use Doctrine\Common\Collections\Collection;
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
 * Individu_to_groupe controller.
 * @RouteResource("individu-group", pluralize=false)
 */
class Individu_to_groupeController extends Controller
{
    private $dateCreationFrom_filter;
    private $dateCreationTo_filter;
    private $propriete_filter;
    private $description_filter;
    private $utilisateur_filter;

    /**
     * Liste un ou (tous) les groupes de son proprietaire contenant des individus
     *
     * @param Request $request
     * @param Groupe_relationnel $groupe_relationnel
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Get("/group/{id}", name="s_groupe")
     */
    public function getAction(Request $request, Groupe_relationnel $groupe_relationnel)
    {
        $this->listeAndShowSecurity($groupe_relationnel);
        $individu_to_groupes = $groupe_relationnel->getGroupeIndividus();
        if ($request->isXmlHttpRequest() && $request->getMethod() === "POST") {
            $this->dateCreationFrom_filter = $request->request->has('dateCreationFrom_filter') ? $request->request->get('dateCreationFrom_filter') : "";
            $this->dateCreationTo_filter = $request->request->has('dateCreationTo_filter') ? $request->request->get('dateCreationTo_filter') : "";
            $this->propriete_filter = $request->request->has('propriete_filter') ? $request->request->get('propriete_filter') : "";
            $this->description_filter = $request->request->has('description_filter') ? $request->request->get('description_filter') : "";
            $this->utilisateur_filter = $request->request->has('utilisateur_filter') ? $request->request->get('utilisateur_filter') : "";
            $iDisplayLength = $request->request->has('length') ? $request->request->get('length') : -1;
            $iDisplayStart = $request->request->has('start') ? intval($request->request->get('start')) : 0;
            $json = array();
            $json['items'] = array();
            $iTotalRecords = count($individu_to_groupes);
            if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
            $individu_to_groupes = $this->handleResults($individu_to_groupes, $iTotalRecords, $iDisplayStart, $iDisplayLength);
            //filtre
            /** @var Individu_to_groupe $individu_groupe */
            foreach ($individu_to_groupes as $individu_groupe) {
                array_push($json['items'], array(
                    'id' => $individu_groupe->getId(),
                    'individu' => $individu_groupe->getIndividu()->getId(),
                    'groupe' => $individu_groupe->getGroupeRelationnel()->getId(),
                    'propriete' => $individu_groupe->getPropriete()
                ));
            }
            return $this->json(json_encode($json), 200);
        }

        return $this->render('APMUserBundle:individu_to_groupe:index.html.twig', [
                'individu_to_groupes' => $individu_to_groupes,
                'groupe' => $groupe_relationnel,

            ]
        );
    }

    /**
     * @param Groupe_relationnel $groupe
     */
    private function listeAndShowSecurity($groupe)
    {
        //---------------------------------security-----------------------------------------------
        // Liste tous les groupes auxquels l'utilisateur appartient
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw $this->createAccessDeniedException();
        }
        if ($groupe) {
            $isGroupMember = false;
            /** @var Utilisateur_avm $user */
            $user = $this->getUser();
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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Get("/new/relation/group/{id}", name="_group")
     */
    public function newAction(Request $request, Groupe_relationnel $groupe_relationnel)
    {
        $this->createSecurity($groupe_relationnel);
        /** @var Session $session */
        $session = $request->getSession();
        /** @var Individu_to_groupe $individu_to_groupe */
        $individu_to_groupe = TradeFactory::getTradeProvider("individu_to_groupe");
        $form = $this->createForm('APM\UserBundle\Form\Individu_to_groupeType', $individu_to_groupe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->createSecurity($groupe_relationnel, $individu_to_groupe->getIndividu());
                $individu_to_groupe->setGroupeRelationnel($groupe_relationnel);
                $em = $this->getDoctrine()->getManager();
                if ($request->isXmlHttpRequest()) {
                    $json = array();
                    $json["item"] = array(//prevenir le client
                        "action" => 0,
                    );
                    $session->getFlashBag()->add('success', "insertion d'un individu dans le groupe: <strong> " . $groupe_relationnel->getDesignation() . "</strong><br> Opération effectuée avec succès!");
                    return $this->json(json_encode($json), 200);
                }
                $em->persist($individu_to_groupe);
                $em->flush();
                return $this->redirectToRoute('apm_user_individu-to-groupe_show', array('id' => $individu_to_groupe->getId()));
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
        return $this->render('APMUserBundle:individu_to_groupe:new.html.twig', array(
            'groupe_relationnel' => $groupe_relationnel,
            'form' => $form->createView(),
        ));
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
     * @param Request $request
     * @param Individu_to_groupe $individu_to_groupe
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Get("/show/relation/{id}")
     */
    public function showAction(Request $request, Individu_to_groupe $individu_to_groupe)
    {
        $this->listeAndShowSecurity($individu_to_groupe->getGroupeRelationnel());
        if ($request->isXmlHttpRequest()) {
            $json = array();
            $json['item'] = array(
                'id' => $individu_to_groupe->getId(),
                'date' => $individu_to_groupe->getDateInsertion(),
                'propriete' => $individu_to_groupe->getPropriete(),
                'individu' => $individu_to_groupe->getIndividu()->getId(),
                'groupe' => $individu_to_groupe->getGroupeRelationnel()->getId(),
            );
            return $this->json(json_encode($json), 200);
        }
        $deleteForm = $this->createDeleteForm($individu_to_groupe);
        return $this->render('APMUserBundle:individu_to_groupe:show.html.twig', array(
            'individu_to_groupe' => $individu_to_groupe,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Individu_to_groupe entity.
     *
     * @param Individu_to_groupe $individu_to_groupe The Individu_to_groupe entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Individu_to_groupe $individu_to_groupe)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_user_individu-to-groupe_delete', array('id' => $individu_to_groupe->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Individu_to_groupe entity.
     * @param Request $request
     * @param Individu_to_groupe $individu_to_groupe
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Put("/edit/relation/{id}")
     */
    public function editAction(Request $request, Individu_to_groupe $individu_to_groupe)
    {
        $this->editAndDeleteSecurity($individu_to_groupe);
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
                    $individu_to_groupe->setPropriete($value);
                    break;
                case 'propriete' :
                    $individu_to_groupe->setPropriete($value);
                    break;
                default:
                    $session->getFlashBag()->add('info', "<strong> Aucune mis à jour effectuée</strong>");
                    return $this->json(json_encode(["item" => null]), 205);
            }
            $em->flush();
            $session->getFlashBag()->add('success', "Mis à jour propriété de l'individu : <strong>" . $property . "</strong>  <br> Opération effectuée avec succès!");
            return $this->json(json_encode($json), 200);
        }
        $deleteForm = $this->createDeleteForm($individu_to_groupe);
        $editForm = $this->createForm('APM\UserBundle\Form\Individu_to_groupeType', $individu_to_groupe);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->editAndDeleteSecurity($individu_to_groupe);
            $em = $this->getDoctrine()->getManager();
            $em->persist($individu_to_groupe);
            $em->flush();

            return $this->redirectToRoute('apm_user_individu-to-groupe_show', array('id' => $individu_to_groupe->getId()));
        }

        return $this->render('APMUserBundle:individu_to_groupe:edit.html.twig', array(
            'individu_to_groupe' => $individu_to_groupe,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
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
     * Deletes a Individu_to_groupe entity.
     * @param Request $request
     * @param Individu_to_groupe $individu_to_groupe
     * @return \Symfony\Component\HttpFoundation\RedirectResponse | JsonResponse
     *
     * @Delete("/delete/relation/{id}")
     */
    public function deleteAction(Request $request, Individu_to_groupe $individu_to_groupe)
    {
        $this->editAndDeleteSecurity($individu_to_groupe);
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {
            $json = array();
            $json['item'] = array();
            $em->remove($individu_to_groupe);
            $em->flush();
            return $this->json($json, 200);
        }
        $form = $this->createDeleteForm($individu_to_groupe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->remove($individu_to_groupe);
            $em->flush();
        }

        return $this->redirectToRoute('apm_user_individu-to-groupe_index');
    }
}
