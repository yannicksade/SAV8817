<?php

namespace APM\UserBundle\Controller;

use APM\UserBundle\Entity\Commentaire;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\UserBundle\Factory\TradeFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Commentaire controller.
 *
 */
class CommentaireController extends Controller
{
    /**
     * Lists all Commentaire entities.
     *
     */
    public function indexAction()
    {
        $this->listAndShowSecurity();
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $commentaires = $user->getCommentaires();
        return $this->render('APMUserBundle:commentaire:index.html.twig', array(
            'commentaires' => $commentaires,
        ));
    }

    /**
     * Creates a new Commentaire entity.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $this->createSecurity();
        /** @var Commentaire $commentaire */
        $commentaire = TradeFactory::getTradeProvider("commentaire");
        $form = $this->createForm('APM\UserBundle\Form\CommentaireType', $commentaire);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->createSecurity();
            $commentaire->setUtilisateur($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($commentaire);
            $em->flush();

            return $this->redirectToRoute('apm_user_commentaire_show', array('id' => $commentaire->getId()));
        }

        return $this->render('APMUserBundle:commentaire:new.html.twig', array(
            'commentaire' => $commentaire,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Commentaire entity.
     * @param Commentaire $commentaire
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Commentaire $commentaire)
    {
        $this->listAndShowSecurity();
        $deleteForm = $this->createDeleteForm($commentaire);

        return $this->render('APMUserBundle:commentaire:show.html.twig', array(
            'commentaire' => $commentaire,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Commentaire entity.
     *
     * @param Commentaire $commentaire The Commentaire entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Commentaire $commentaire)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_user_commentaire_delete', array('id' => $commentaire->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Commentaire entity.
     * @param Request $request
     * @param Commentaire $commentaire
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Commentaire $commentaire)
    {
        $this->editAndDeleteSecurity($commentaire);

        $deleteForm = $this->createDeleteForm($commentaire);
        $editForm = $this->createForm('APM\UserBundle\Form\CommentaireType', $commentaire);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->editAndDeleteSecurity($commentaire);
            $em = $this->getDoctrine()->getManager();
            $em->persist($commentaire);
            $em->flush();

            return $this->redirectToRoute('apm_user_commentaire_show', array('id' => $commentaire->getId()));
        }

        return $this->render('APMUserBundle:commentaire:edit.html.twig', array(
            'commentaire' => $commentaire,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Commentaire entity.
     * @param Request $request
     * @param Commentaire $commentaire
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Commentaire $commentaire)
    {
        $this->editAndDeleteSecurity($commentaire);
        $form = $this->createDeleteForm($commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->editAndDeleteSecurity($commentaire);
            $em = $this->getDoctrine()->getManager();
            $em->remove($commentaire);
            $em->flush();
        }
        return $this->redirectToRoute('apm_user_commentaire_index');
    }

    public function deleteFromListAction(Commentaire $object)
    {
        $this->editAndDeleteSecurity($commentaire);
        $em = $this->getDoctrine()->getManager();
        $em->remove($object);
        $em->flush();

        return $this->redirectToRoute('apm_user_commentaire_index');
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
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @param Commentaire $commentaire
     */
    private function editAndDeleteSecurity($commentaire){
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        /* ensure that the user is logged in
        *  and that the one is the owner
        */
        $user = $this->getUser();
        if ((!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) || ($commentaire->getUtilisateur() !== $user)) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------

    }

}
