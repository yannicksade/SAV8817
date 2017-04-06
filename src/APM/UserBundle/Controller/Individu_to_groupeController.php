<?php

namespace APM\UserBundle\Controller;

use APM\UserBundle\Entity\Groupe_relationnel;
use APM\UserBundle\Entity\Individu_to_groupe;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\UserBundle\Factory\TradeFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Individu_to_groupe controller.
 *
 */
class Individu_to_groupeController extends Controller
{
    /**
     * Liste tous les groupes auxquels l'utilisateur appartient
     * AUCUNE ACCESS PREVUE POUR CETTE FONCTION;
     *
     */
    public function indexAction()
    {
        $this->listeAndShowSecurity();
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $individu_to_groupes = $user->getIndividuGroupes();

        return $this->render('APMUserBundle:individu_to_groupe:index.html.twig', array(
            'individu_to_groupes' => $individu_to_groupes,
        ));
    }

    /**
     *  Affecter l'utilisateur à un groupe
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $this->createSecurity();
        /** @var Individu_to_groupe $individu_to_groupe */
        $individu_to_groupe = TradeFactory::getTradeProvider("individu_to_groupe");
        $form = $this->createForm('APM\UserBundle\Form\Individu_to_groupeType', $individu_to_groupe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->createSecurity($form->getData()['groupeRelationnel']);
            $em = $this->getDoctrine()->getManager();
            $em->persist($individu_to_groupe);
            $em->flush();

            return $this->redirectToRoute('apm_user_individu-to-groupe_show', array('id' => $individu_to_groupe->getId()));
        }

        return $this->render('APMUserBundle:individu_to_groupe:new.html.twig', array(
            'individu_to_groupe' => $individu_to_groupe,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Individu_to_groupe entity.
     * @param Individu_to_groupe $individu_to_groupe
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Individu_to_groupe $individu_to_groupe)
    {
        $this->listeAndShowSecurity();
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
     */
    public function editAction(Request $request, Individu_to_groupe $individu_to_groupe)
    {
        $this->editAndDeleteSecurity($individu_to_groupe);

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
     * Deletes a Individu_to_groupe entity.
     * @param Request $request
     * @param Individu_to_groupe $individu_to_groupe
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Individu_to_groupe $individu_to_groupe)
    {
        $this->editAndDeleteSecurity($individu_to_groupe);

        $form = $this->createDeleteForm($individu_to_groupe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->editAndDeleteSecurity($individu_to_groupe);
            $em = $this->getDoctrine()->getManager();
            $em->remove($individu_to_groupe);
            $em->flush();
        }

        return $this->redirectToRoute('apm_user_individu-to-groupe_index');
    }

    public function deleteFromListAction(Individu_to_groupe $individu_to_groupe)
    {
        $this->editAndDeleteSecurity($individu_to_groupe);

        $em = $this->getDoctrine()->getManager();
        $em->remove($individu_to_groupe);
        $em->flush();

        return $this->redirectToRoute('apm_user_individu-to-groupe_index');
    }

    private function listeAndShowSecurity()
    {
        //---------------------------------security-----------------------------------------------
        // Liste tous les groupes auxquels l'utilisateur appartient
        $this->denyAccessUnlessGranted('ROLE_NO_ACCESS', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw $this->createAccessDeniedException();
        }
        //-----------------------------------------------------------------------------------------
    }

    /**
     * @param Groupe_relationnel $groupe
     */
    private function createSecurity($groupe = null)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');

        $user = $this->getUser();
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') || $user !== $groupe->getProprietaire()) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
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
}
