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
use APM\TransportBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Boutique;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class Transporteur_zoneInterventionController extends Controller
{
    /**
     * Liste les zone d'intervention auxquelles appartient le transporteur
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $this->listeAndShowSecurity();
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $zoneInterventions = $user->getTransporteur()->getTransporteurZones();
        return $this->render('APMTransportBundle:zone_intervention:index.html.twig', array('$zoneInterventionsEnregistrees' => $zoneInterventions));
    }

    private function listeAndShowSecurity()
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_TRANSPORTEUR', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        //-----------------------------------------------------------------------------------------
    }

    public function newAction(Request $request)
    {
        $this->createSecurity($transporteur);

        /** @var Transporteur_zoneintervention $transporteur_zoneIntervention */
        $transporteur_zoneIntervention = TradeFactory::getTradeProvider('transporteur_zoneIntervention');
        $form = $this->createForm('APM\TransportBundle\Transporteur_zoneInterventionType',$transporteur_zoneIntervention);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->createSecurity($form->getData()['transporteur']);
            $em = $this->getDoctrine()->getManager();
            $em->persist($transporteur_zoneIntervention);
            $em->flush();
        }
        return $this->render('APMTransportBundle:zone_intervention:new.html.twig', array('transporteur_zoneIntervention' => $transporteur_zoneIntervention));
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
    private function createDeleteForm(Transporteur_zoneintervention $transporteur_zoneintervention)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_transporteur_zoneintervention_delete', array('id' => $transporteur_zoneintervention->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @param Profile_transporteur $transporteur
     */
    private function createSecurity($transporteur)
    {
        //----------------security: Ajouter par le gerant boutique ou le transporteur freelance-------------------
        $this->denyAccessUnlessGranted(['ROLE_TRANSPORTEUR', 'ROLE_BOUTIQUE'], null, 'Unable to access this page!');
        /** @var Utilisateur_avm $user */
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')){
            throw $this->createAccessDeniedException();}
        $user = $this->getUser();
         $livreur_boutique = $transporteur->getLivreurBoutique();
        if($livreur_boutique) {
            $boutiques = $livreur_boutique->getBoutiques();
            /** @var Boutique $boutique */
            foreach ($boutiques as $boutique) {
                $gerant = $boutique->getGerant() || $boutique->getProprietaire();
                if ($gerant === $this->getUser()) {
                    break;
                }
            }
            if ($user !== $gerant) {
                throw $this->createAccessDeniedException();
            }
        }
        //----------------------------------------------------------------------------------------
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

    /**
     * @param Transporteur_zoneintervention $transporteur_zoneintervention
     */
    private function editAndDeleteSecurity($transporteur_zoneintervention)
    {
        //------------------------security: Modifier ou supprimme par le gerant boutique ou le transporteur freelance-----------------
        // Unable to access the controller unless they have the required role
        $this->denyAccessUnlessGranted(['ROLE_TRANSPORTEUR' ,'ROLE_BOUTIQUE'], null, 'Unable to access this page!');
        /** @var Utilisateur_avm $user */
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')){
            throw $this->createAccessDeniedException();
        }
        $user = $this->getUser();
        $livreur_boutique = $transporteur_zoneintervention->getTransporteur()->getLivreurBoutique();
        if($livreur_boutique) {
            $boutiques = $livreur_boutique->getBoutiques();
            /** @var Boutique $boutique */
            foreach ($boutiques as $boutique) {
                $gerant = $boutique->getGerant() || $boutique->getProprietaire();
                if ($gerant === $this->getUser()) {
                    break;
                }
            }
                if ($user !== $gerant) {
                    throw $this->createAccessDeniedException();
                }
        }
        //---------------------------------------------------------------------------------------------------------------------------------
    }
}
