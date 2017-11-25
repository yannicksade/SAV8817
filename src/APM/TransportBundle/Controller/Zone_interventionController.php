<?php

namespace APM\TransportBundle\Controller;

use APM\TransportBundle\Entity\Profile_transporteur;
use APM\TransportBundle\Entity\Transporteur_zoneintervention;
use APM\TransportBundle\Entity\Zone_intervention;
use APM\TransportBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Patch;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Zone_intervention controller.
 * @RouteResource("zoneintervention")
 */
class Zone_interventionController extends Controller
{
    private $code_filter;
    private $designation_filter;
    private $description_filter;
    private $adresse_filter;
    private $pays_filter;
    private $transporteur_filter;

    /**
     * liste toutes les zones d'intervention
     * @param Request $request
     * @return Response | JsonResponse
     *
     * @Get("/zoneinterventions")
     */
    public function getAction(Request $request)
    {
        $this->listeAndShowSecurity();
        $em = $this->getDoctrine()->getManager();
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $transporteur = $user->getTransporteur();
        if ($request->isXmlHttpRequest()) {
            $q = $request->query->get('q');
            $this->designation_filter = $request->request->has('designation_filter') ? $request->request->get('designation_filter') : "";
            $this->code_filter = $request->request->has('code_filter') ? $request->request->get('code_filter') : "";
            $this->description_filter = $request->request->has('description_filter') ? $request->request->get('description_filter') : "";
            $this->adresse_filter = $request->request->has('adresse_filter') ? $request->request->get('adresse_filter') : "";
            $this->pays_filter = $request->request->has('pays_filter') ? $request->request->get('pays_filter') : "";
            $this->transporteur_filter = $request->request->has('transporteur_filter') ? $request->request->get('transporteur_filter') : "";
            $iDisplayLength = $request->request->has('length') ? $request->request->get('length') : -1;
            $iDisplayStart = $request->request->has('start') ? intval($request->request->get('start')) : 0;
            $json = array();
            $json['items'] = array();
            if ($q === "guest" || $q === "all") {
                $transporteur_zones = (null !== $transporteur) ? $transporteur->getTransporteurZones() : null;
                if ($transporteur_zones !== null) {
                    $zones = array();
                    /** @var Transporteur_zoneintervention $t_z */
                    foreach ($transporteur_zones as $t_z) {
                        array_push($zones, $t_z->getZoneIntervention());
                    }
                    $zones = new ArrayCollection($zones);
                    $iTotalRecords = count($zones);
                    if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
                    $zones = $this->handleResults($zones, $iTotalRecords, $iDisplayStart, $iDisplayLength);
                    /** @var Zone_intervention $zone */
                    foreach ($zones as $zone) {
                        array_push($json['items'], array(
                            'id' => $zone->getId(),
                            'code' => $zone->getCode(),
                            'designation' => $zone->getDesignation(),
                            'description' => $zone->getDescription(),
                        ));
                    }
                }
            }
            if ($q === "owner" || $q === "all") {
                $zones = (null !== $transporteur) ? $transporteur->getZones() : null;
                if (null !== $zones) {
                    $iTotalRecords = count($zones);
                    if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
                    $zones = $this->handleResults($zones, $iTotalRecords, $iDisplayStart, $iDisplayLength);
                    /** @var Zone_intervention $zone */
                    foreach ($zones as $zone) {
                        array_push($json['items'], array(
                            'id' => $zone->getId(),
                            'code' => $zone->getCode(),
                            'designation' => $zone->getDesignation(),
                            'description' => $zone->getDescription(),
                        ));
                    }
                }
            }

            return $this->json(json_encode($json), 200);
        }

        if (null !== $transporteur) {
            $zonesPropres = $transporteur->getZones();
            $transporteur_zones = $transporteur->getTransporteurZones();
            if ($transporteur_zones !== null) {
                $zonesPartagees = null;
                /** @var Transporteur_zoneintervention $t_z */
                foreach ($transporteur_zones as $t_z) {
                    $zonesPartagees[] = $t_z->getZoneIntervention();
                }
            }
        }
        return $this->render('APMTransportBundle:zone_intervention:index.html.twig', array(
            'zoneInterventions' => $zonesPartagees,
            'zoneInterventionsCreees' => $zonesPropres,
            'transporteur' => $transporteur,
        ));
    }

    private function listeAndShowSecurity()
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
     * @param Collection $zones
     * @param $iTotalRecords
     * @param $iDisplayStart
     * @param $iDisplayLength
     * @return array
     */
    private function handleResults($zones, $iTotalRecords, $iDisplayStart, $iDisplayLength)
    {
        //filtering
        if ($zones === null) return array();

        if ($this->code_filter != null) {
            $zones = $zones->filter(function ($e) {//filtrage select
                /** @var Zone_intervention $e */
                return $e->getCode() === $this->code_filter;
            });
        }
        if ($this->pays_filter != null) {
            $zones = $zones->filter(function ($e) {//filtrage select
                /** @var Zone_intervention $e */
                return $e->getTransporteur() === $this->pays_filter;
            });
        }

        if ($this->transporteur_filter != null) {
            $zones = $zones->filter(function ($e) {//filter with the begining of the entering word
                /** @var Zone_intervention $e */
                $str1 = $e->getTransporteur()->getCode();
                $str2 = $this->transporteur_filter;
                $len = strlen($str2);
                return strncasecmp($str1, $str2, $len) === 0 ? true : false;
            });
        }
        if ($this->designation_filter != null) {
            $zones = $zones->filter(function ($e) {//search for occurences in the text
                /** @var Zone_intervention $e */
                $subject = $e->getDesignation();
                $pattern = $this->designation_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }
        if ($this->description_filter != null) {
            $zones = $zones->filter(function ($e) {//search for occurences in the text
                /** @var Zone_intervention $e */
                $subject = $e->getDescription();
                $pattern = $this->designation_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }
        if ($this->adresse_filter != null) {
            $zones = $zones->filter(function ($e) {//search for occurences in the text
                /** @var Zone_intervention $e */
                $subject = $e->getDescription();
                $pattern = $this->adresse_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }

        $zones = ($zones !== null) ? $zones->toArray() : [];
        //assortment: descending of date -- du plus recent au plus ancient
        usort(
            $zones, function ($e1, $e2) {
            /**
             * @var Zone_intervention $e1
             * @var Zone_intervention $e2
             */
            $dt1 = $e1->getDateEnregistrement()->getTimestamp();
            $dt2 = $e2->getDateEnregistrement()->getTimestamp();
            return $dt1 <= $dt2 ? 1 : -1;
        });
        if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
        //paging; slice and preserve keys' order
        $zones = array_slice($zones, $iDisplayStart, $iDisplayLength, true);

        return $zones;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse|RedirectResponse|Response
     * @Post("/new/zoneintervention/{id}")
     */
    public function newAction(Request $request)
    {
        $this->createSecurity();

        /** @var Zone_intervention $zone_intervention */
        $zone_intervention = TradeFactory::getTradeProvider("zone_intervention");
        $form = $this->createForm('APM\TransportBundle\Form\Zone_interventionType', $zone_intervention);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $transporteur = $form->get('transporteur')->getData();
            $this->createSecurity($transporteur);
            if (!$transporteur) $zone_intervention->setTransporteur($this->getUser()->getTransporteur());
            $em = $this->getDoctrine()->getManager();
            $em->persist($zone_intervention);
            $em->flush();
            if ($request->isXmlHttpRequest()) {
                $json = array();
                $json['item'] = array();
                return $this->json(json_encode($json), 200);
            }
            return $this->redirectToRoute('apm_zone_intervention_show', array('id' => $zone_intervention->getId()));
        }

        return $this->render('APMTransportBundle:zone_intervention:new.html.twig', array(
            'zone_intervention' => $zone_intervention,
            'form' => $form->createView(),
        ));
    }

    /**
     * @param Profile_transporteur $transporteur
     */
    private function createSecurity($transporteur = null)
    {
        //---------------------------------security-----------------------------------------------
        $this->denyAccessUnlessGranted(['ROLE_TRANSPORTEUR', 'ROLE_BOUTIQUE'], null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $gerant = null;
        $proprietaire = null;
        if ($transporteur) {
            $user = $this->getUser();
            $livreur = $transporteur->getLivreurBoutique();
            if ($livreur) {
                $boutique = $livreur->getBoutiqueProprietaire();
                $gerant = $boutique->getGerant();
                $proprietaire = $boutique->getProprietaire();
                if ($user !== $gerant && $user !== $proprietaire) {
                    throw $this->createAccessDeniedException();
                }
            }
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * Finds and displays a Zone_intervention entity.
     * @param Request $request
     * @param Zone_intervention $zone_intervention
     * @return Response | JsonResponse
     *
     * @Get("/show/zoneintervention/{id}")
     */
    public function showAction(Request $request, Zone_intervention $zone_intervention)
    {
        $this->listeAndShowSecurity();
        if ($request->isXmlHttpRequest()) {
            $json = array();
            $json['item'] = array(
                'id' => $zone_intervention->getId(),
                'code' => $zone_intervention->getCode(),
                'description' => $zone_intervention->getDescription(),
                'designation' => $zone_intervention->getDesignation(),
                'adresse' => $zone_intervention->getAdresse(),
                'pays' => $zone_intervention->getPays(),
                'language' => $zone_intervention->getLanguage(),
                'ville' => $zone_intervention->getVille(),
                'createur' => $zone_intervention->getTransporteur()->getId(),
                'zoneTime' => $zone_intervention->getZoneTime(),
            );
            return $this->json(json_encode($json), 200);
        }
        $deleteForm = $this->createDeleteForm($zone_intervention);

        return $this->render('APMTransportBundle:zone_intervention:show.html.twig', array(
            'zone_intervention' => $zone_intervention,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Zone_intervention entity.
     *
     * @param Zone_intervention $zone_intervention The Zone_intervention entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Zone_intervention $zone_intervention)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_zone_intervention_delete', array('id' => $zone_intervention->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Zone_intervention entity.
     * @param Request $request
     * @param Zone_intervention $zone_intervention
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response|JsonResponse
     * @Put("/edit/zoneintervention/{id}")
     */
    public function editAction(Request $request, Zone_intervention $zone_intervention)
    {
        $this->editAndDeleteSecurity($zone_intervention);
        /** @var Session $session */
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest() && $request->getMethod() === "POST") {
            $json = array();
            $json['item'] = array();
            $property = $request->request->get('name');
            $value = $request->request->get('value');
            switch ($property) {
                case 'language':
                    $zone_intervention->setLanguage($value);
                    break;
                case 'description':
                    $zone_intervention->setDescription($value);
                    break;
                case 'designation':
                    $zone_intervention->setDesignation($value);
                    break;
                case 'adresse' :
                    $zone_intervention->setAdresse($value);
                    break;
                case 'pays' :
                    $zone_intervention->setPays($value);
                    break;
                case 'transporteur':
                    /** @var Profile_transporteur $transporteur */
                    $transporteur = $em->getRepository('APMTransporteurBundle:Profile_transporteur')->find($value);
                    /** @var Transporteur_zoneintervention $transporteur_zone */
                    $transporteur_zone = TradeFactory::getTradeProvider("transporteur_zoneIntervention");
                    $transporteur_zone->setTransporteur($transporteur);
                    $zone_intervention->addZoneTransporteur($transporteur_zone);
                    break;
                default:
                    $session->getFlashBag()->add('info', "<strong> Aucune mise à jour effectuée</strong>");
                    return $this->json(json_encode(["item" => null]), 205);
            }
            $em->flush();
            $session->getFlashBag()->add('success', "Mis à jour propriété : <strong>" . $property . "</strong> réf. Boutique :" . $zone_intervention->getCode() . "<br> Opération effectuée avec succès!");
            return $this->json(json_encode($json), 200);
        }
        $deleteForm = $this->createDeleteForm($zone_intervention);
        $editForm = $this->createForm('APM\TransportBundle\Form\Zone_interventionType', $zone_intervention);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->editAndDeleteSecurity($zone_intervention);
            $em = $this->getDoctrine()->getManager();
            $em->persist($zone_intervention);
            $em->flush();


            return $this->redirectToRoute('apm_zone_intervention_show', array('id' => $zone_intervention->getId()));
        }

        return $this->render('APMTransportBundle:zone_intervention:edit.html.twig', array(
            'zone_intervention' => $zone_intervention,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @param Zone_intervention $zone_intervention
     */
    private function editAndDeleteSecurity($zone_intervention)
    { //
        //--------------------------------- security: uniquement la boutique ou le le Transporteur autonome peut modifier et supprimer les ZI -----------------------------------------------
        $this->denyAccessUnlessGranted(['ROLE_TRANSPORTEUR', 'ROLE_BOUTIQUE'], null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $user = $this->getUser();
        $transporteur = $zone_intervention->getTransporteur();
        $livreur = $transporteur->getLivreurBoutique();
        if ($livreur) {
            $boutique = $livreur->getBoutiqueProprietaire();
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
            if ($user !== $gerant && $user !== $proprietaire) {
                throw $this->createAccessDeniedException();
            }

        } else if ($user !== $transporteur->getUtilisateur()) {
            throw $this->createAccessDeniedException();
        }

        //----------------------------------------------------------------------------------------
    }

    /**
     * Deletes a Zone_intervention entity.
     * @param Request $request
     * @param Zone_intervention $zone_intervention
     * @return \Symfony\Component\HttpFoundation\RedirectResponse | JsonResponse
     *
     * @Delete("/delete/zoneintervention/{id}")
     */
    public function deleteAction(Request $request, Zone_intervention $zone_intervention)
    {
        $this->editAndDeleteSecurity($zone_intervention);

        $form = $this->createDeleteForm($zone_intervention);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {
            $em->remove($boutique);
            $em->flush();
            $json = array();
            $json['item'] = array();
            return $this->json(json_encode($json), 200);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $em->remove($zone_intervention);
            $em->flush();
        }

        return $this->redirectToRoute('apm_zone_intervention_index');
    }

}
