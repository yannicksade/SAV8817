<?php

namespace APM\AchatBundle\Controller;

use APM\AchatBundle\Entity\Specification_achat;
use APM\AchatBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Specification_achat controller.
 *
 */
class Specification_achatController extends Controller
{
    /**
     * Liste les Specification faites par le client
     * @return \Symfony\Component\HttpFoundation\Response
     * @internal param Utilisateur $utilisateur
     */
    public function indexAction()
    {
        $this->listAndShowSecurity();
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $specification_achats = $user->getSpecifications();

        return $this->render('APMAchatBundle:specification_achat:index.html.twig', array(
            'specification_achats' => $specification_achats,
        ));
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $this->createSecurity();
        /** @var Specification_achat $specification_achat */
        $specification_achat = TradeFactory::getTradeProvider("specification_achat");
        $form = $this->createForm('APM\AchatBundle\Form\Specification_achatType', $specification_achat);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->createSecurity();
            $specification_achat->setUtilisateur($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($specification_achat);
            $em->flush();

            return $this->redirectToRoute('apm_achat_specification_achat_show', array('id' => $specification_achat->getId()));
        }

        return $this->render('APMAchatBundle:specification_achat:new.html.twig', array(
            'specification_achat' => $specification_achat,
            'form' => $form->createView(),
        ));
    }

    /**
     * Voir une specification faite
     *
     * Finds and displays a Specification_achat entity.
     * @param Specification_achat $specification_achat
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Specification_achat $specification_achat)
    {
        $this->listAndShowSecurity();
        $deleteForm = $this->createDeleteForm($specification_achat);

        return $this->render('APMAchatBundle:specification_achat:show.html.twig', array(
            'specification_achat' => $specification_achat,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Specification_achat entity.
     *
     * @param Specification_achat $specification_achat The Specification_achat entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Specification_achat $specification_achat)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_achat_specification_achat_delete', array('id' => $specification_achat->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Specification_achat entity.
     * @param Request $request
     * @param Specification_achat $specification_achat
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Specification_achat $specification_achat)
    {
        $this->editAndDeleteSecurity($specification_achat);
        $deleteForm = $this->createDeleteForm($specification_achat);
        $editForm = $this->createForm('APM\AchatBundle\Form\Specification_achatType', $specification_achat);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->editAndDeleteSecurity($specification_achat);
            $em = $this->getDoctrine()->getManager();
            $em->persist($specification_achat);
            $em->flush();

            return $this->redirectToRoute('apm_achat_specification_achat_show', array('id' => $specification_achat->getId()));
        }

        return $this->render('APMAchatBundle:specification_achat:edit.html.twig', array(
            'specification_achat' => $specification_achat,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Specification_achat entity.
     * @param Request $request
     * @param Specification_achat $specification_achat
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Specification_achat $specification_achat)
    {
        $this->editAndDeleteSecurity($specification_achat);

        $form = $this->createDeleteForm($specification_achat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->editAndDeleteSecurity($specification_achat);
            $em = $this->getDoctrine()->getManager();
            $em->remove($specification_achat);
            $em->flush();
        }

        return $this->redirectToRoute('apm_achat_specification_achat_index');
    }

    /**
     * @param Specification_achat $specification_achat
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteFromListAction(Specification_achat $specification_achat)
    {
         $this->editAndDeleteSecurity($specification_achat);
        $em = $this->getDoctrine()->getManager();
        $em->remove($specification_achat);
        $em->flush();

        return $this->redirectToRoute('apm_achat_specification_achat_index');
    }

    private function listAndShowSecurity(){
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
    private function createSecurity(){
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
     * @param Specification_achat $specification_achat
     */
    private function editAndDeleteSecurity($specification_achat){
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');

        /* ensure that the user is logged in  # granted even through remembering cookies
        */
        $user = $this->getUser();
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED') || $user !== $specification_achat->getUtilisateur()) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }
}
