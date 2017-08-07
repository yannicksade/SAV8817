<?php

namespace APM\TransportBundle\Controller;

use APM\TransportBundle\Entity\Profile_transporteur;
use APM\TransportBundle\Entity\Zone_intervention;
use APM\TransportBundle\Factory\TradeFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Zone_intervention controller.
 *
 */
class Zone_interventionController extends Controller
{
    /**
     * liste toutes les zones d'intervention
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $this->listeAndShowSecurity();
        $em = $this->getDoctrine()->getManager();
        $AllZones = $em->getRepository('APMTransportBundle:Zone_intervention')->findAll();

        return $this->render('APMTransportBundle:zone_intervention:index_old.html.twig', array(
            'zoneInterventions' => $AllZones,
            'zoneInterventionsCreees' => null,
            'transporteur' => null,
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
     * @param Zone_intervention $zone_intervention
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Zone_intervention $zone_intervention)
    {
        $this->listeAndShowSecurity();

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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Zone_intervention $zone_intervention)
    {
        $this->editAndDeleteSecurity($zone_intervention);

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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Zone_intervention $zone_intervention)
    {
        $this->editAndDeleteSecurity($zone_intervention);

        $form = $this->createDeleteForm($zone_intervention);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->editAndDeleteSecurity($zone_intervention);
            $em = $this->getDoctrine()->getManager();
            $em->remove($zone_intervention);
            $em->flush();
        }

        return $this->redirectToRoute('apm_zone_intervention_index');
    }

    public function deleteFromListAction(Zone_intervention $zone_intervention)
    {
        $this->editAndDeleteSecurity($zone_intervention);

        $em = $this->getDoctrine()->getManager();
        $em->remove($zone_intervention);
        $em->flush();

        return $this->redirectToRoute('apm_zone_intervention_index');
    }
}
