<?php

namespace APM\TransportBundle\Controller;


use APM\TransportBundle\Entity\Livreur_boutique;
use APM\TransportBundle\Entity\Profile_transporteur;
use APM\TransportBundle\Entity\Zone_intervention;
use APM\TransportBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Zone_intervention controller.
 *
 */
class Zone_interventionController extends Controller
{
    /**
     * acceder les zones d'intervention créée par le transporteur
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $this->listeAndShowSecurity();
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $zone_interventions = $user->getTransporteur()->getZones();
        return $this->render('APMTransportBundle:zone_intervention:index.html.twig', array(
            'zone_interventions' => $zone_interventions,
        ));
    }

    private function listeAndShowSecurity()
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted( 'ROLE_TRANSPORTEUR', null, 'Unable to access this page!');
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
            $this->createSecurity();
            /** @var Livreur_boutique $livreur_boutique */
            $livreur_boutique = $form->getData()['livreur_boutique'];
            /** @var Profile_transporteur $transporteur */
            $transporteur = $this->getUser()->getTransporteur()|| $livreur_boutique->getTransporteur();
            $zone_intervention->setTransporteur($transporteur);
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

    private function createSecurity()
    {
        //---------------------------------security-----------------------------------------------
        $this->denyAccessUnlessGranted(['ROLE_TRANSPORTEUR', 'ROLE_BOUTIQUE'], null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') ) {
            throw $this->createAccessDeniedException();
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
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless they have the required role
        $this->denyAccessUnlessGranted(['ROLE_TRANSPORTEUR','ROLE_BOUTIQUE'], null, 'Unable to access this page!');
        /** @var Utilisateur_avm $user */
       $user = $this->getUser();
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') || $user !== $zone_intervention->getTransporteur()->getUtilisateur()){
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
