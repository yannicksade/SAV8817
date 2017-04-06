<?php

namespace APM\UserBundle\Controller;

use APM\UserBundle\Entity\Groupe_relationnel;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\UserBundle\Factory\TradeFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Groupe_relationnel controller.
 *
 */
class Groupe_relationnelController extends Controller
{
    /**
     * Liste tous les groupe relationnel crée par l'utilisateur
     *
     */
    public function indexAction()
    {
        $this->listAndShowSecurity();
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $groupes = $user->getGroupesProprietaire();
        return $this->render('APMUserBundle:groupe_relationnel:index.html.twig', array(
            'groupe_relationnels' => $groupes
        ));
    }

    /**
     * Creates a new Groupe_relationnel entity.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $this->createSecurity();
        /** @var Groupe_relationnel $groupe_relationnel */
        $groupe_relationnel = TradeFactory::getTradeProvider("groupe_relationnel");
        $form = $this->createForm('APM\UserBundle\Form\Groupe_relationnelType', $groupe_relationnel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->createSecurity();
            $groupe_relationnel->setProprietaire($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($groupe_relationnel);
            $em->flush();

            return $this->redirectToRoute('apm_user_groupe-relationnel_show', array('id' => $groupe_relationnel->getId()));
        }

        return $this->render('APMUserBundle:groupe_relationnel:new.html.twig', array(
            'groupe_relationnel' => $groupe_relationnel,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Groupe_relationnel entity.
     * @param Groupe_relationnel $groupe_relationnel
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Groupe_relationnel $groupe_relationnel)
    {
        $this->listAndShowSecurity();
        $deleteForm = $this->createDeleteForm($groupe_relationnel);

        return $this->render('APMUserBundle:groupe_relationnel:show.html.twig', array(
            'groupe_relationnel' => $groupe_relationnel,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Groupe_relationnel entity.
     *
     * @param Groupe_relationnel $groupe_relationnel The Groupe_relationnel entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Groupe_relationnel $groupe_relationnel)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_user_groupe-relationnel_delete', array('id' => $groupe_relationnel->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Groupe_relationnel entity.
     * @param Request $request
     * @param Groupe_relationnel $groupe_relationnel
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Groupe_relationnel $groupe_relationnel)
    {
        $this->editAndDeleteSecurity($groupe_relationnel);
        $deleteForm = $this->createDeleteForm($groupe_relationnel);
        $editForm = $this->createForm('APM\UserBundle\Form\Groupe_relationnelType', $groupe_relationnel);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->editAndDeleteSecurity($groupe_relationnel);
            $em = $this->getDoctrine()->getManager();
            $em->persist($groupe_relationnel);
            $em->flush();

            return $this->redirectToRoute('apm_user_groupe-relationnel_show', array('id' => $groupe_relationnel->getId()));
        }

        return $this->render('APMUserBundle:groupe_relationnel:edit.html.twig', array(
            'groupe_relationnel' => $groupe_relationnel,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Groupe_relationnel entity.
     * @param Request $request
     * @param Groupe_relationnel $groupe_relationnel
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Groupe_relationnel $groupe_relationnel)
    {
        $this->editAndDeleteSecurity($groupe_relationnel);
        $form = $this->createDeleteForm($groupe_relationnel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->editAndDeleteSecurity($groupe_relationnel);
            $em = $this->getDoctrine()->getManager();
            $em->remove($groupe_relationnel);
            $em->flush();
        }

        return $this->redirectToRoute('apm_user_groupe-relationnel_index');
    }

    public function deleteFromListAction(Groupe_relationnel $groupe_relationnel)
    {
        $this->editAndDeleteSecurity($groupe_relationnel);
        $em = $this->getDoctrine()->getManager();
        $em->remove($groupe_relationnel);
        $em->flush();

        return $this->redirectToRoute('apm_user_groupe-relationnel_index');
    }

    private function listAndShowSecurity(){
        //-----------------------------------security-------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED'))  {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    private function createSecurity(){
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        /* ensure that the user is logged in
        */
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @param Groupe_relationnel $groupe
     * @internal param Commentaire $commentaire
     */
    private function editAndDeleteSecurity($groupe){
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');

        /* ensure that the user is logged in
        *  and that the one is the owner
        */
        $user = $this->getUser();
        if ((!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) || ($groupe->getProprietaire() !== $user)) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------

    }

}
