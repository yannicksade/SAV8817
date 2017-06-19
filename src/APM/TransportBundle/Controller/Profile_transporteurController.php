<?php

namespace APM\TransportBundle\Controller;

use APM\TransportBundle\Entity\Profile_transporteur;
use APM\TransportBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Profile_transporteur controller.
 *
 */
class Profile_transporteurController extends Controller
{
    /**
     * fonction search des transporteurs
     * @param string $name
     * @param $value
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($name = null, $value = null)
    {
        $this->listeAndShowSecurity();
        $em = $this->getDoctrine()->getManager();
        if ($name || $value) {
            $profile_transporteurs = $em->getRepository('APMTransportBundle:Profile_transporteur')->findBy([$name => $value]);
        } else {
            $profile_transporteurs = $em->getRepository('APMTransportBundle:Profile_transporteur')->findAll();
        }

        return $this->render('APMTransportBundle:profile_transporteur:index.html.twig', array(
            'profile_transporteurs' => $profile_transporteurs,
            'zone' => null,
        ));
    }

    private function listeAndShowSecurity()
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
     * Creates a new Profile_transporteur entity.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $this->createSecurity();
        /** @var Profile_transporteur $profile_transporteur */
        $profile_transporteur = TradeFactory::getTradeProvider("transporteur");
        $form = $this->createForm('APM\TransportBundle\Form\Profile_transporteurType', $profile_transporteur);
        $form->remove('utilisateur');
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->createSecurity();
            $profile_transporteur->setUtilisateur($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($profile_transporteur);
            $em->flush();

            return $this->redirectToRoute('apm_transport_transporteur_show', array('id' => $profile_transporteur->getId()));
        }

        return $this->render('APMTransportBundle:profile_transporteur:new.html.twig', array(
            'profile_transporteur' => $profile_transporteur,
            'form' => $form->createView(),
        ));
    }

    private function createSecurity()
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_TRANSPORTEUR', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        //vérifier si l'utilisateur n'est pas déjà enregistré comme transporteur ou livreur
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $utilisateur = null;
        $utilisateur = $em->getRepository('APMTransportBundle:Profile_transporteur')->findOneBy(['utilisateur' => $user->getId()]);
        if (null !== $utilisateur) throw $this->createAccessDeniedException();
        //----------------------------------------------------------------------------------------
    }

    /**
     * Finds and displays a Profile_transporteur entity.
     * @param Profile_transporteur $profile_transporteur
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Profile_transporteur $profile_transporteur)
    {
        $this->listeAndShowSecurity();
        $deleteForm = $this->createDeleteForm($profile_transporteur);

        return $this->render('APMTransportBundle:profile_transporteur:show.html.twig', array(
            'profile_transporteur' => $profile_transporteur,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Profile_transporteur entity.
     *
     * @param Profile_transporteur $profile_transporteur The Profile_transporteur entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Profile_transporteur $profile_transporteur)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_transport_transporteur_delete', array('id' => $profile_transporteur->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Profile_transporteur entity.
     * @param Request $request
     * @param Profile_transporteur $profile_transporteur
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Profile_transporteur $profile_transporteur)
    {
        $this->editAndDeleteSecurity($profile_transporteur);
        $deleteForm = $this->createDeleteForm($profile_transporteur);
        $editForm = $this->createForm('APM\TransportBundle\Form\Profile_transporteurType', $profile_transporteur);
        $editForm->remove('utilisateur');
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($profile_transporteur);
            $em->flush();

            return $this->redirectToRoute('apm_transport_transporteur_show', array('id' => $profile_transporteur->getId()));
        }

        return $this->render('APMTransportBundle:profile_transporteur:edit.html.twig', array(
            'profile_transporteur' => $profile_transporteur,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @param Profile_transporteur $transporteur
     */
    private function editAndDeleteSecurity($transporteur)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_TRANSPORTEUR', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        //autoriser la modification uniquement qau transporteur autonome de droit exclut tout livreur boutique
        $user = $this->getUser();
        if ($transporteur->getLivreurBoutique() || $user !== $transporteur->getUtilisateur()) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * Deletes a Profile_transporteur entity.
     * @param Request $request
     * @param Profile_transporteur $profile_transporteur
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Profile_transporteur $profile_transporteur)
    {
        $this->editAndDeleteSecurity($profile_transporteur);
        $form = $this->createDeleteForm($profile_transporteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($profile_transporteur);
            $em->flush();
        }

        return $this->redirectToRoute('apm_transport_transporteur_index');
    }

    public function deleteFromListAction(Profile_transporteur $profile_transporteur)
    {
        $this->editAndDeleteSecurity($profile_transporteur);

        $em = $this->getDoctrine()->getManager();
        $em->remove($profile_transporteur);
        $em->flush();

        return $this->redirectToRoute('apm_transport_transporteur_index');
    }
}