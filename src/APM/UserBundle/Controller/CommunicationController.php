<?php

namespace APM\UserBundle\Controller;

use APM\UserBundle\Entity\Communication;
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
 * Communication controller.
 * @RouteResource("communication", pluralize=false)
 */
class CommunicationController extends Controller
{
    private $code_filter;
    private $contenu_filter;
    private $etat_filter;
    private $reference_filter;
    private $date_filter;
    private $type_filter;
    private $valide_filter;
    private $emetteur_filter;
    private $recepteur_filter;
    private $dateDeVigueurFrom_filter;
    private $dateDeVigueurTo_filter;
    private $dateFinFrom_filter;
    private $dateFinTo_filter;

    /**
     * Lister les communication reçues et envoyées par un utilisateur
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Get("/cget/communications", name="s")
     */
    public function getAction(Request $request)
    {
        $this->listAndShowSecurity();
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        if ($request->isXmlHttpRequest() && $request->getMethod() === "POST") {
            $q = $request->get('q');
            $this->dateDeVigueurFrom_filter = $request->request->has('dateDeVigueurFrom_filter') ? $request->request->get('dateDeVigueurFrom_filter') : "";
            $this->dateDeVigueurTo_filter = $request->request->has('dateDeVigueurTo_filter') ? $request->request->get('dateDeVigueurTo_filter') : "";
            $this->code_filter = $request->request->has('code_filter') ? $request->request->get('code_filter') : "";
            $this->contenu_filter = $request->request->has('contenu_filter') ? $request->request->get('contenu_filter') : "";
            $this->etat_filter = $request->request->has('etat_filter') ? $request->request->get('etat_filter') : "";
            $this->reference_filter = $request->request->has('reference_filter') ? $request->request->get('reference_filter') : "";
            $this->dateFinFrom_filter = $request->request->has('dateFinFrom_filter') ? $request->request->get('dateFinFrom_filter') : "";
            $this->dateFinTo_filter = $request->request->has('dateFinTo_filter') ? $request->request->get('dateFinTo_filter') : "";
            $this->date_filter = $request->request->has('date_filter') ? $request->request->get('date_filter') : "";
            $this->type_filter = $request->request->has('type_filter') ? $request->request->get('type_filter') : "";
            $this->valide_filter = $request->request->has('valide_filter') ? $request->request->get('valide_filter') : "";
            $this->emetteur_filter = $request->request->has('emetteur_filter') ? $request->request->get('emetteur_filter') : "";
            $this->recepteur_filter = $request->request->has('recepteur_filter') ? $request->request->get('recepteur_filter') : "";
            $iDisplayLength = $request->request->has('length') ? $request->request->get('length') : -1;
            $iDisplayStart = $request->request->has('start') ? intval($request->request->get('start')) : 0;

            $json = array();
            if ($q === "sent" || $q === "all") {
                $communicationsSent = $user->getEmetteurCommunications();
                $iTotalRecords = count($communicationsSent);
                if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
                $communicationsSent = $this->handleResults($communicationsSent, $iTotalRecords, $iDisplayStart, $iDisplayLength);
                //filtre
                /** @var Communication $communication */
                foreach ($communicationsSent as $communication) {
                    array_push($json, array(
                        'id' => $communication->getId(),
                        'objet' => $communication->getObjet(),
                    ));
                }
            }
            if ($q === "received" || $q === "all") {
                $communicationsReceived = $user->getRecepteurCommunications();
                $iTotalRecords = count($communicationsReceived);
                if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
                $communicationsReceived = $this->handleResults($communicationsReceived, $iTotalRecords, $iDisplayStart, $iDisplayLength);
                foreach ($communicationsReceived as $communication) {
                    array_push($json, array(
                        'id' => $communication->getId(),
                        'code' => $communication->getCode(),
                        'object' => $communication->getObjet(),
                    ));
                }
            }
            return $this->json(json_encode($json), 200);
        }

        $communicationsSent = $user->getEmetteurCommunications();
        $communicationsReceived = $user->getRecepteurCommunications();

        return $this->render('APMUserBundle:communication:index.html.twig', array(
            'communicationsSent' => $communicationsSent,
            'communicationsReceived' => $communicationsReceived,
        ));
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
     * @param Collection $communications
     * @param $iTotalRecords
     * @param $iDisplayStart
     * @param $iDisplayLength
     * @return array
     */
    private function handleResults($communications, $iTotalRecords, $iDisplayStart, $iDisplayLength)
    {
        //filtering
        if ($communications === null) return array();

        if ($this->code_filter != null) {
            $communications = $communications->filter(function ($e) {//filtrage select
                /** @var Communication $e */
                return $e->getCode() === $this->code_filter;
            });
        }
        if ($this->etat_filter != null) {
            $communications = $communications->filter(function ($e) {//filtrage select
                /** @var Communication $e */
                return $e->getEtat() === $this->etat_filter;
            });
        }
        if ($this->type_filter != null) {
            $communications = $communications->filter(function ($e) {//filtrage select
                /** @var Communication $e */
                return $e->getType() === $this->type_filter;
            });
        }
        if ($this->valide_filter != null) {
            $communications = $communications->filter(function ($e) {//filtrage select
                /** @var Communication $e */
                return $e->getValide() === boolval($this->valide_filter);
            });
        }
        if ($this->dateDeVigueurFrom_filter != null) {
            $communications = $communications->filter(function ($e) {//start date
                /** @var Communication $e */
                $dt1 = (new \DateTime($e->getDateDeVigueur()->format('d-m-Y')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateDeVigueurFrom_filter))->getTimestamp();
                return $dt1 - $dt2 >= 0 ? true : false; //start from the given date 'dateFrom_filter'
            });
        }
        if ($this->dateDeVigueurTo_filter != null) {
            $communications = $communications->filter(function ($e) {//end date
                /** @var Communication $e */
                $dt = (new \DateTime($e->getDateDeVigueur()->format('d-m-Y')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateDeVigueurTo_filter))->getTimestamp();
                return $dt - $dt2 <= 0 ? true : false;// end from at the given date 'dateTo_filter'
            });
        }
        if ($this->dateFinFrom_filter != null) {
            $communications = $communications->filter(function ($e) {//start date
                /** @var Communication $e */
                $dt1 = (new \DateTime($e->getDateFin()->format('d-m-Y')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateFinFrom_filter))->getTimestamp();
                return $dt1 - $dt2 >= 0 ? true : false; //start from the given date 'dateFrom_filter'
            });
        }
        if ($this->dateFinTo_filter != null) {
            $communications = $communications->filter(function ($e) {//end date
                /** @var Communication $e */
                $dt = (new \DateTime($e->getDateFin()->format('d-m-Y')))->getTimestamp();
                $dt2 = (new \DateTime($this->dateFinTo_filter))->getTimestamp();
                return $dt - $dt2 <= 0 ? true : false;// end from at the given date 'dateTo_filter'
            });
        }
        if ($this->emetteur_filter != null) {
            $communications = $communications->filter(function ($e) {//search for occurences in the text
                /** @var Communication $e */
                $subject = $e->getEmetteur();
                $pattern = $this->emetteur_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }
        if ($this->recepteur_filter != null) {
            $communications = $communications->filter(function ($e) {//search for occurences in the text
                /** @var Communication $e */
                $subject = $e->getRecepteur();
                $pattern = $this->recepteur_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }

        $communications = ($communications !== null) ? $communications->toArray() : [];
        //assortment: descending of date -- du plus recent au plus ancient
        usort(
            $communications, function ($e1, $e2) {
            /**
             * @var Communication $e1
             * @var Communication $e2
             */
            $dt1 = $e1->getDate()->getTimestamp();
            $dt2 = $e2->getDate()->getTimestamp();
            return $dt1 <= $dt2 ? 1 : -1;
        });
        if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
        //paging; slice and preserve keys' order
        $communications = array_slice($communications, $iDisplayStart, $iDisplayLength, true);

        return $communications;
    }

    /**
     * L'Emetteur Crée et soumet un model de communication
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response | JsonResponse
     *
     * @Post("/new/communication")
     */
    public function newAction(Request $request)
    {
        $this->createSecurity();
        /** @var Session $session */
        $session = $request->getSession();
        /** @var Communication $communication */
        $communication = TradeFactory::getTradeProvider("communication");
        $form = $this->createForm('APM\UserBundle\Form\CommunicationType', $communication);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->createSecurity();
                $communication->setEmetteur($this->getUser());
                $em = $this->getDoctrine()->getManager();
                $em->persist($communication);
                $em->flush();
                if ($request->isXmlHttpRequest()) {
                    $json = array();
                    $json["item"] = array(//prevenir le client
                        "action" => 0,
                    );
                    $session->getFlashBag()->add('success', "<strong> rabais d'offre créée. réf:" . $communication->getCode() . "</strong><br> Opération effectuée avec succès!");
                    return $this->json(json_encode($json), 200);
                }
                return $this->redirectToRoute('apm_user_communication_show', array('id' => $communication->getId()));
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
        return $this->render('APMUserBundle:communication:new.html.twig', array(
            'communication' => $communication,
            'form' => $form->createView(),
        ));
    }

    private function createSecurity()
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');

        /* ensure that the user is logged in */
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * Finds and displays a Communication entity.
     * @param Communication $communication
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Get("/show/communication/{id}")
     */
    public function showAction(Communication $communication)
    {
        $this->listAndShowSecurity();
        if ($request->isXmlHttpRequest()) {
            $json = array();
            $json['item'] = array(
                'id' => $communication->getId(),
                'code' => $communication->getCode(),
                'objet' => $communication->getObjet(),
                'dateDeVigueur' => $communication->getDateDeVigueur()->format('d-m-Y H:i'),
                'dateFin' => $communication->getDateFin()->format('d-m-Y H:i'),
                'contenu' => $communication->getContenu(),
                'date' => $communication->getDate()->format('d-m-Y H:i'),
                'reference' => $communication->getReference(),
                'etat' => $communication->getEtat(),
                'emetteur' => $communication->getEmetteur()->getId(),
                'recepteur' => $communication->getRecepteur()->getId(),
                'type' => $communication->getType(),
                'valide' => $communication->getValide(),
            );
            return $this->json(json_encode($json), 200);
        }
        $deleteForm = $this->createDeleteForm($communication);
        return $this->render('APMUserBundle:communication:show.html.twig', array(
            'communication' => $communication,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Communication entity.
     *
     * @param Communication $communication The Communication entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Communication $communication)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_user_communication_delete', array('id' => $communication->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Communication entity.
     * @param Request $request
     * @param Communication $communication
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Put("/edit/communication/{id}")
     */
    public function editAction(Request $request, Communication $communication)
    {
        $this->editAndDeleteSecurity($communication);
        if ($request->isXmlHttpRequest() && $request->getMethod() === "POST") {
            $json = array();
            $json['item'] = array();
            /** @var Session $session */
            $session = $request->getSession();
            $em = $this->getDoctrine()->getManager();
            $property = $request->request->get('name');
            $value = $request->request->get('value');
            switch ($property) {
                case 'dateDeVigueur':
                    $communication->setDatedevigueur($value);
                    break;
                case 'dateFin' :
                    $communication->setDateFin($value);
                    break;
                case 'reference' :
                    $communication->setReference($value);
                    break;
                case 'etat' :
                    $communication->setEtat($value);
                    break;
                case 'type' :
                    $communication->setType($value);
                    break;
                default:
                    $session->getFlashBag()->add('info', "<strong> Aucune mis à jour effectuée</strong>");
                    return $this->json(json_encode(["item" => null]), 205);
            }
            $em->flush();
            $session->getFlashBag()->add('success', "Mis à jour propriété : <strong>" . $property . "</strong> réf. communication :" . $communication->getCode() . "<br> Opération effectuée avec succès!");
            return $this->json(json_encode($json), 200);
        }
        $deleteForm = $this->createDeleteForm($communication);
        $editForm = $this->createForm('APM\UserBundle\Form\CommunicationType', $communication);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->editAndDeleteSecurity($communication);
            $em = $this->getDoctrine()->getManager();
            $em->persist($communication);
            $em->flush();

            return $this->redirectToRoute('apm_user_communication_show', array('id' => $communication->getId()));
        }

        return $this->render('APMUserBundle:communication:edit.html.twig', array(
            'communication' => $communication,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @param Communication $communication
     */
    private function editAndDeleteSecurity($communication)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');

        /* ensure that the user is logged in
        *  and that the one is the owner
        */
        $user = $this->getUser();
        if ((!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) || ($communication->getEmetteur() !== $user)) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------

    }

    /**
     * Deletes a Communication entity.
     * @param Request $request
     * @param Communication $communication
     * @return \Symfony\Component\HttpFoundation\RedirectResponse | JsonResponse
     *
     * @Delete("/delete/communication/{id}")
     */
    public function deleteAction(Request $request, Communication $communication)
    {
        $this->editAndDeleteSecurity($communication);
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest() && $request->getMethod() === "POST") {
            $em->remove($communication);
            $em->flush();
            $json = array();
            return $this->json($json, 200);
        }
        $form = $this->createDeleteForm($communication);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->editAndDeleteSecurity($communication);
            $em->remove($communication);
            $em->flush();
        }

        return $this->redirectToRoute('apm_user_communication_index');
    }

}
