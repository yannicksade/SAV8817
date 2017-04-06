<?php

namespace APM\AnimationBundle\Controller;

use APM\AnimationBundle\Entity\Base_documentaire;
use APM\AnimationBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Base_documentaire controller.
 *
 */
class Base_documentaireController extends Controller
{
    /**
     *Liste les documents de l'utilisateur
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $documents = $user->getDocuments();
        return $this->render('APMAnimationBundle:document:index.html.twig', array(
            'documents' => $documents,
        ));
    }

    /**
     * Creates a new Base_documentaire entity.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $this->createSecurity();
        /** @var Base_documentaire $document */
        $document = TradeFactory::getTradeProvider("base_documentaire");
        $form = $this->createForm('APM\AnimationBundle\Form\Base_documentaireType', $document);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->createSecurity();
            $document->setProprietaire($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($document);
            $em->flush();

            return $this->redirectToRoute('apm_animation_base_documentaire_show', array('id' => $document->getId()));
        }

        return $this->render('APMAnimationBundle:document:new.html.twig', array(
            'document' => $document,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Base_documentaire entity.
     * @param Base_documentaire $document
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Base_documentaire $document)
    {
        $this->listAndShowSecurity();
        $deleteForm = $this->createDeleteForm($document);

        return $this->render('APMAnimationBundle:document:show.html.twig', array(
            'document' => $document,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Base_documentaire entity.
     *
     * @param Base_documentaire $document The Base_documentaire entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Base_documentaire $document)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_animation_base_documentaire_delete', array('id' => $document->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Base_documentaire entity.
     * @param Request $request
     * @param Base_documentaire $document
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Base_documentaire $document)
    {
        $this->editAndDeleteSecurity($document);

        $deleteForm = $this->createDeleteForm($document);
        $editForm = $this->createForm('APM\AnimationBundle\Form\Base_documentaireType', $document);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->editAndDeleteSecurity($document);
            $em = $this->getDoctrine()->getManager();
            $em->persist($document);
            $em->flush();

            return $this->redirectToRoute('apm_animation_base_documentaire_show', array('id' => $document->getId()));
        }

        return $this->render('APMAnimationBundle:document:edit.html.twig', array(
            'document' => $document,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Base_documentaire entity.
     * @param Request $request
     * @param Base_documentaire $document
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Base_documentaire $document)
    {
        $this->editAndDeleteSecurity($document);

        $form = $this->createDeleteForm($document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->editAndDeleteSecurity($document);
            $em = $this->getDoctrine()->getManager();
            $em->remove($document);
            $em->flush();
        }

        return $this->redirectToRoute('apm_animation_base_documentaire_index');
    }

    public function deleteFromListAction(Base_documentaire $document)
    {
        $this->editAndDeleteSecurity($document);
        $em = $this->getDoctrine()->getManager();
        $em->remove($document);
        $em->flush();

        return $this->redirectToRoute('apm_animation_base_documentaire_index');
    }

    /**
     * @param Base_documentaire $document
     */
    private function editAndDeleteSecurity($document){
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');

        /* ensure that the user is logged in  # granted even through remembering cookies
        *  and that the one is the owner
        */
        $user = $this->getUser();
        if ((!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) || ($document->getProprietaire() !== $user)) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    private function listAndShowSecurity(){
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');

        /* ensure that the user is logged in  # granted even through remembering cookies
        *  and that the one is the owner
        */
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    private function createSecurity(){
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');

        /* ensure that the user is logged in  # granted even through remembering cookies
        *  and that the one is the owner
        */
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }
}
