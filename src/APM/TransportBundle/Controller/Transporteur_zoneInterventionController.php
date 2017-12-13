<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 02/04/2017
 * Time: 10:07
 */

namespace APM\TransportBundle\Controller;

use APM\TransportBundle\Entity\Profile_transporteur;
use APM\TransportBundle\Entity\Transporteur_zoneintervention;
use APM\TransportBundle\Entity\Zone_intervention;
use APM\TransportBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Patch;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Class Transporteur_zoneInterventionController
 * @RouteResource("transporteur-zoneIntervention", pluralize=false)
 */
class Transporteur_zoneInterventionController extends Controller
{
    private $transporteur_filter;
    private $zoneIntervention_filter;

    /**
     * @ParamConverter("zone_intervention", options={"mapping": {"zone_id":"id"}})
     * @param Request $request
     * @param Profile_transporteur $transporteur
     * @param Zone_intervention $zone_intervention
     * @return JsonResponse
     *
     * @Get("/cget/transporteurs/zone/{zone_id}", name="s_zone")
     * @Get("/cget/zones/transporteur/{id}", name="s_transporteur")
     */
    public function getAction(Request $request, Profile_transporteur $transporteur = null, Zone_intervention $zone_intervention = null)
    {
        $this->listeAndShowSecurity();
        if (null !== $transporteur) {
            $transporteurs_zones = $transporteur->getTransporteurZones();
        } else if (null !== $zone_intervention) {
            $transporteurs_zones = $zone_intervention->getZoneTransporteurs();
        }
        $json = array();
        $this->transporteur_filter = $request->request->has('transporteur_filter') ? $request->request->get('transporteur_filter') : "";
        $this->zoneIntervention_filter = $request->request->has('zoneIntervention_filter') ? $request->request->get('zoneIntervention_filter') : "";
        $iDisplayLength = $request->request->has('length') ? $request->request->get('length') : -1;
        $iDisplayStart = $request->request->has('start') ? intval($request->request->get('start')) : 0;
        $iTotalRecords = count($transporteurs_zones);
        if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
        $transporteurs_zones = $this->handleResults($transporteurs_zones, $iTotalRecords, $iDisplayStart, $iDisplayLength);
        $iFilteredRecords = count($transporteurs_zones);
        $data = $this->get('apm_core.data_serialized')->getFormalData($transporteurs_zones, array("owner_list"));
        $json['totalRecords'] = $iTotalRecords;
        $json['filteredRecords'] = $iFilteredRecords;
        $json['items'] = $data;

        return new JsonResponse($json, 200);
    }

    private function listeAndShowSecurity()
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        //-----------------------------------------------------------------------------------------
    }

    /**
     * @param Collection $transporteurs_zones
     * @param $iTotalRecords
     * @param $iDisplayStart
     * @param $iDisplayLength
     * @return array
     */
    private function handleResults($transporteurs_zones, $iTotalRecords, $iDisplayStart, $iDisplayLength)
    {
        //filtering
        if ($transporteurs_zones === null) return array();
        if ($this->transporteur_filter != null) {
            $transporteurs_zones = $transporteurs_zones->filter(function ($e) {//search for occurences in the text
                /** @var Transporteur_zoneintervention $e */
                $subject = $e->getTransporteur()->getUtilisateur()->getUsername();
                $pattern = $this->transporteur_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }
        if ($this->zoneIntervention_filter != null) {
            $transporteurs_zones = $transporteurs_zones->filter(function ($e) {//search for occurences in the text
                /** @var Transporteur_zoneintervention $e */
                $subject = $e->getZoneIntervention()->getDesignation();
                $pattern = $this->zoneIntervention_filter;
                return preg_match('/' . $pattern . '/i', $subject) === 1 ? true : false;
            });
        }
        $transporteurs_zones = ($transporteurs_zones !== null) ? $transporteurs_zones->toArray() : [];
        //assortment: descending of date -- du plus recent au plus ancient
        usort(
            $transporteurs_zones, function ($e1, $e2) {
            /**
             * @var  Transporteur_zoneintervention $e1
             * @var  Transporteur_zoneintervention $e2
             */
            $dt1 = $e1->getDateEnregistrement()->getTimestamp();
            $dt2 = $e2->getDateEnregistrement()->getTimestamp();
            return $dt1 <= $dt2 ? 1 : -1;
        });
        if ($iDisplayLength < 0) $iDisplayLength = $iTotalRecords;
        //paging; slice and preserve keys' order
        $transporteurs_zones = array_slice($transporteurs_zones, $iDisplayStart, $iDisplayLength, true);

        return $transporteurs_zones;
    }


    //insérer un transporteur dans une zone d'intervention; transporteur; boutique

    /**
     * @param Request $request
     * @param Profile_transporteur $transporteur
     * @param Zone_intervention|null $zone_intervention
     * @return \Symfony\Component\HttpFoundation\Response | JsonResponse
     *
     * @Post("/new/transporteur/{id}/zone/{zone_id}", name="_transporteur_zone")
     *
     */
    public function newAction(Request $request, Profile_transporteur $transporteur, Zone_intervention $zone_intervention)
    {
        $this->createSecurity($transporteur);
        /** @var Transporteur_zoneintervention $transporteur_zoneIntervention */
        $transporteur_zoneIntervention = TradeFactory::getTradeProvider('transporteur_zoneIntervention');
        $form = $this->createForm('APM\TransportBundle\Transporteur_zoneInterventionType', $transporteur_zoneIntervention);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (null !== $zone_intervention) $transporteur_zoneIntervention->setZoneIntervention($zone_intervention);
            if (null !== $transporteur) $transporteur_zoneIntervention->setTransporteur($transporteur);
            $em = $this->getDoctrine()->getManager();
            $em->persist($transporteur_zoneIntervention);
            $em->flush();
            if ($request->isXmlHttpRequest()) {
                $json = array();
                $json['item'] = array();
                return $this->json(json_encode($json), 200);
            }
        }
        return $this->render('APMTransportBundle:zone_intervention:new.html.twig', array('transporteur_zoneIntervention' => $transporteur_zoneIntervention));
    }

    /**
     * @param Profile_transporteur $transporteur
     *
     */
    private function createSecurity($transporteur)
    {
        //----------------security: Ajouter par le proprietaire, le gerant boutique ou le transporteur freelance-------------------
        $this->denyAccessUnlessGranted(['ROLE_TRANSPORTEUR', 'ROLE_BOUTIQUE'], null, 'Unable to access this page!');
        /** @var Utilisateur_avm $user */
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        $user = $this->getUser();
        if (null === $transporteur) $transporteur = $user->getTransporteur();
        if ($user !== $transporteur->getUtilisateur()) throw $this->createAccessDeniedException();
        $livreur_boutique = $transporteur->getLivreurBoutique();
        if (null !== $livreur_boutique) {
            $boutique = $livreur_boutique->getBoutiqueProprietaire();
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
            if ($gerant !== $user && $proprietaire !== $user) {
                throw $this->createAccessDeniedException();
            }
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @param Transporteur_zoneintervention $transporteur_zoneintervention
     * @return JsonResponse
     *
     * @Get("/show/transporteur-zone/{id}")
     */
    public function showAction(Transporteur_zoneintervention $transporteur_zoneintervention)
    {
        $this->editAndDeleteSecurity($transporteur_zoneintervention);
        $data = $this->get('apm_core.data_serialized')->getFormalData($transporteur_zoneintervention, ["owner_transporteurZ_details", "owner_list"]);
        return new JsonResponse($data, 200);
    }

    /**
     * @param Transporteur_zoneintervention $transporteur_zoneintervention
     */
    private function editAndDeleteSecurity($transporteur_zoneintervention)
    {
        //------------------------security: Modifier ou supprimme par le gerant boutique ou le transporteur freelance-----------------
        // Unable to access the controller unless they have the required role
        $this->denyAccessUnlessGranted(['ROLE_TRANSPORTEUR', 'ROLE_BOUTIQUE'], null, 'Unable to access this page!');
        /** @var Utilisateur_avm $user */
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $user = $this->getUser();
        $livreur_boutique = $transporteur_zoneintervention->getTransporteur()->getLivreurBoutique();
        if ($livreur_boutique) {
            $boutique = $livreur_boutique->getBoutiqueProprietaire();
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
            if ($gerant !== $user && $proprietaire !== $user) {
                throw $this->createAccessDeniedException();
            }

        } else if ($user !== $transporteur->getUtilisateur()) throw $this->createAccessDeniedException();

        //---------------------------------------------------------------------------------------------------------------------------------
    }

    /**
     * @param Request $request
     * @param Transporteur_zoneintervention $transporteur_zoneintervention
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Post("/edit/transporteur-zone/{id}")
     */
    public function editAction(Request $request, Transporteur_zoneintervention $transporteur_zoneintervention)
    {
        $this->editAndDeleteSecurity($transporteur_zoneintervention);

        $deleteForm = $this->createDeleteForm($transporteur_zoneintervention);
        $editForm = $this->createForm('APM\TransportBundle\Form\Transporteur_zoneInterventionType', $transporteur_zoneintervention);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid() || $request->isXmlHttpRequest() && $request->getMethod() === "POST") {
            $this->editAndDeleteSecurity($transporteur_zoneintervention);
            $em = $this->getDoctrine()->getManager();
            if ($request->isXmlHttpRequest()) {
                $json = array();
                $json["item"] = array();
                $property = $request->request->get('name');
                $value = $request->request->get('value');
                switch ($property) {
                    case 'transporteur':
                        /** @var Profile_transporteur $transporteur */
                        $transporteur = $em->getRepository('APMTransportBundle:Profile_transporteur')->find($value);
                        $transporteur_zoneintervention->setTransporteur($transporteur);
                        break;
                    case 'zone':
                        /** @var Zone_intervention $zone */
                        $zone = $em->getRepository('APMTransportBundle:Zone_intervention')->find($value);
                        $transporteur_zoneintervention->setZoneIntervention($zone);
                        break;
                    default:
                        $session->getFlashBag()->add('info', "<strong> Aucune mise à jour effectuée</strong>");
                        return $this->json(json_encode(["item" => null]), 205);
                }
                $em->flush();
                $session->getFlashBag()->add('success', "Mise à jour propriété : <strong>" . $property . "</strong> <br> Opération effectuée avec succès!");
                return $this->json(json_encode($json), 200);
            }
            $em->persist($transporteur_zoneintervention);
            $em->flush();
            return $this->redirectToRoute('apm_transporteur_zoneintervention_show', array('id' => $transporteur_zoneintervention->getId()));
        }

        return $this->render('APMTransportBundle:transporteur_zoneintervention:edit.html.twig', array(
            'transporteur_zoneintervention' => $transporteur_zoneintervention,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),));
    }

    private function createDeleteForm(Transporteur_zoneintervention $transporteur_zoneintervention)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_transporteur_zoneintervention_delete', array('id' => $transporteur_zoneintervention->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Deletes a Transporteur_zoneintervention entity.
     * @param Request $request
     * @param Transporteur_zoneintervention $transporteur_zoneintervention
     * @return \Symfony\Component\HttpFoundation\RedirectResponse | JsonResponse
     *
     * @Post("/delete/transporteur-zone/{id}")
     */
    public function deleteAction(Request $request, Transporteur_zoneintervention $transporteur_zoneintervention)
    {
        $this->editAndDeleteSecurity($transporteur_zoneintervention);
        $em = $this->getDoctrine()->getManager();
        $em->remove($transporteur_zoneintervention);
        $em->flush();

        if ($request->isXmlHttpRequest()) {
            $json = array();
            $json['item'] = array();
            return $this->json(json_encode($json), 200);
        }

        return $this->redirectToRoute('apm_transport_transporteur_index');
    }

}
