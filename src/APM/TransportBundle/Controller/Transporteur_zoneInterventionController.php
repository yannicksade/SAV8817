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
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class Transporteur_zoneInterventionController extends Controller
{
    /**
     * Liste les zone d'intervention créées et celles auxquelles appartient le transporteur
     * Liste aussi les zones d'interventions propres et d'appartenance d'un transporteur donné
     * @param Profile_transporteur $transporteur
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Profile_transporteur $transporteur = null)
    {
        $this->listeAndShowSecurity();
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $zoneInterventions = null;
        if ($transporteur) {
            $zoneInterventionsCreees = $transporteur->getZones(); //zones propres dudit transporteur
            $transporteurZones = $transporteur->getTransporteurZones(); //zones d'appartenance dudit transporteur
        } else {
            $zoneInterventionsCreees = $user->getTransporteur()->getZones(); //zones propres du transporteur courant
            $transporteurZones = $user->getTransporteur()->getTransporteurZones(); //zones d'appartenance du transporteur courant
        }

        /** @var Transporteur_zoneintervention $transporteurZone */
        foreach ($transporteurZones as $transporteurZone) {
            $zoneInterventions [] = $transporteurZone->getZoneIntervention();
        }
        return $this->render('APMTransportBundle:zone_intervention:index_old.html.twig', array(
                'zoneInterventions' => $zoneInterventions,
                'transporteur' => $transporteur,
                'zoneInterventionsCreees' => $zoneInterventionsCreees,
            )
        );
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
     * @param Zone_intervention|null $zone_intervention
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexTransporteursAction(Zone_intervention $zone_intervention)
    {
        $this->listeAndShowSecurity();

        $transporteurs = null;
        $zoneTransporteurs = $zone_intervention->getZoneTransporteurs(); //Transporteurs appartenant à ladite zone d'intervention
        $transporteurs [] = $zone_intervention->getTransporteur(); // ajout du transporteur principal.
        /** @var Transporteur_zoneintervention $zoneTransporteur */
        foreach ($zoneTransporteurs as $zoneTransporteur) {
            $transporteurs [] = $zoneTransporteur->getTransporteur();
        }
        return $this->render('APMTransportBundle:profile_transporteur:index_old.html.twig', array(
                'profile_transporteurs' => $transporteurs,
                'zone' => $zone_intervention,
            )
        );
    }

    //insérer un transporteur dans une zone d'intervention; transporteur; boutique

    /**
     * @param Request $request
     * @param Profile_transporteur $transporteur
     * @param Zone_intervention|null $zone_intervention
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request, Profile_transporteur $transporteur = null, Zone_intervention $zone_intervention = null)
    {
        $this->createSecurity($transporteur);

        /** @var Transporteur_zoneintervention $transporteur_zoneIntervention */
        $transporteur_zoneIntervention = TradeFactory::getTradeProvider('transporteur_zoneIntervention');
        $form = $this->createForm('APM\TransportBundle\Transporteur_zoneInterventionType', $transporteur_zoneIntervention);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$transporteur) $transporteur = $form->get('transporteur')->getData();
            if ($zone_intervention) $transporteur_zoneIntervention->setZoneIntervention($zone_intervention);
            $this->createSecurity($transporteur);
            $transporteur_zoneIntervention->setTransporteur($transporteur);
            $em = $this->getDoctrine()->getManager();
            $em->persist($transporteur_zoneIntervention);
            $em->flush();
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
        $livreur_boutique = $transporteur->getLivreurBoutique();
        if ($livreur_boutique) {
            $boutique = $livreur_boutique->getBoutiqueProprietaire();
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
            if ($gerant !== $user && $proprietaire !== $user) {
                throw $this->createAccessDeniedException();
            }
        } else if ($user !== $transporteur->getUtilisateur()) throw $this->createAccessDeniedException();
        //----------------------------------------------------------------------------------------
    }

    public function showAction(Transporteur_zoneintervention $transporteur_zoneintervention)
    {
        $this->editAndDeleteSecurity($transporteur_zoneintervention);
        $deleteForm = $this->createDeleteForm($transporteur_zoneintervention);

        return $this->render('APMTransportBundle:transporteur_zoneintervention:show.html.twig', array(
            'zone_intervention' => $zone_intervention,
            'delete_form' => $deleteForm->createView(),
        ));
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

    private function createDeleteForm(Transporteur_zoneintervention $transporteur_zoneintervention)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_transporteur_zoneintervention_delete', array('id' => $transporteur_zoneintervention->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    public function editAction(Request $request, Transporteur_zoneintervention $transporteur_zoneintervention)
    {
        $this->editAndDeleteSecurity($transporteur_zoneintervention);

        $deleteForm = $this->createDeleteForm($transporteur_zoneintervention);
        $editForm = $this->createForm('APM\TransportBundle\Form\Transporteur_zoneInterventionType', $transporteur_zoneintervention);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->editAndDeleteSecurity($transporteur_zoneintervention);
            $em = $this->getDoctrine()->getManager();
            $em->persist($transporteur_zoneintervention);
            $em->flush();

            return $this->redirectToRoute('apm_transporteur_zoneintervention_show', array('id' => $transporteur_zoneintervention->getId()));
        }

        return $this->render('APMTransportBundle:transporteur_zoneintervention:edit.html.twig', array(
            'transporteur_zoneintervention' => $transporteur_zoneintervention,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),));
    }

    /**
     * Deletes a Transporteur_zoneintervention entity.
     * @param Request $request
     * @param Transporteur_zoneintervention $transporteur_zoneintervention
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Transporteur_zoneintervention $transporteur_zoneintervention)
    {
        $this->editAndDeleteSecurity($transporteur_zoneintervention);

        $form = $this->createDeleteForm($transporteur_zoneintervention);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->editAndDeleteSecurity($transporteur_zoneintervention);
            $em = $this->getDoctrine()->getManager();
            $em->remove($transporteur_zoneintervention);
            $em->flush();
        }

        return $this->redirectToRoute('apm_transport_transporteur_index');
    }

    public function deleteFromListAction(Transporteur_zoneintervention $transporteur_zoneintervention)
    {
        $this->editAndDeleteSecurity($transporteur_zoneintervention);
        $em = $this->getDoctrine()->getManager();
        $em->remove($transporteur_zoneintervention);
        $em->flush();

        return $this->redirectToRoute('apm_transport_transporteur_index');
    }
}
