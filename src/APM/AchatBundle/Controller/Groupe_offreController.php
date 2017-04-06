<?php

namespace APM\AchatBundle\Controller;

use APM\AchatBundle\Entity\Groupe_offre;
use APM\AchatBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Groupe_offre controller.
 * Liste les Groupe d'offre crees par l'utilisateur
 */
class Groupe_offreController extends Controller
{

    public function indexAction()
    {
        $this->listAndShowSecurity();
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $groupe_offres = $user->getGroupesOffres();
        return $this->render('APMAchatBundle:groupe_offre:index.html.twig', array('groupe_offres' => $groupe_offres
        ));
    }

    /**
     * Creates a new Groupe_offre entity.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $this->createSecurity();

        /** @var Groupe_offre $groupe_offre */
        $groupe_offre = TradeFactory::getTradeProvider("groupe_offre");
        $form = $this->createForm('APM\AchatBundle\Form\Groupe_offreType', $groupe_offre);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->createSecurity();
            $groupe_offre->setCreateur($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($groupe_offre);
            $em->flush();

            return $this->redirectToRoute('apm_achat_groupe_show', array('id' => $groupe_offre->getId()));
        }

        return $this->render('APMAchatBundle:groupe_offre:new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Groupe_offre entity.
     * @param Groupe_offre $groupe_offre
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Groupe_offre $groupe_offre)
    {
        $this->listAndShowSecurity($groupe_offre);

        $deleteForm = $this->createDeleteForm($groupe_offre);

        return $this->render('APMAchatBundle:groupe_offre:show.html.twig', array(
            'groupe_offre' => $groupe_offre,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Groupe_offre entity.
     *
     * @param Groupe_offre $groupe_offre The Groupe_offre entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Groupe_offre $groupe_offre)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_achat_groupe_delete', array('id' => $groupe_offre->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Groupe_offre entity.
     * @param Request $request
     * @param Groupe_offre $groupe_offre
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Groupe_offre $groupe_offre)
    {
        $this->editAndDeleteSecurity($groupe_offre);

        $deleteForm = $this->createDeleteForm($groupe_offre);
        $editForm = $this->createForm('APM\AchatBundle\Form\Groupe_offreType', $groupe_offre);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->editAndDeleteSecurity($groupe_offre);
            $em = $this->getDoctrine()->getManager();
            $em->persist($groupe_offre);
            $em->flush();

            return $this->redirectToRoute('apm_achat_groupe_show', array('id' => $groupe_offre->getId()));
        }

        return $this->render('APMAchatBundle:groupe_offre:edit.html.twig', array(
            'groupe_offre' => $groupe_offre,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Groupe_offre entity.
     * @param Request $request
     * @param Groupe_offre $groupe_offre
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Groupe_offre $groupe_offre)
    {
        $this->editAndDeleteSecurity($groupe_offre);

        $form = $this->createDeleteForm($groupe_offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->editAndDeleteSecurity($groupe_offre);
            $em = $this->getDoctrine()->getManager();
            $em->remove($groupe_offre);
            $em->flush();
        }

        return $this->redirectToRoute('apm_achat_groupe_index');
    }

    public function deleteFromListAction(Groupe_offre $groupe_offre)
    {
        $this->editAndDeleteSecurity($groupe_offre);

        $em = $this->getDoctrine()->getManager();
        $em->remove($groupe_offre);
        $em->flush();

        return $this->redirectToRoute('apm_achat_groupe_index');
    }

    private function listAndShowSecurity(Groupe_offre $groupe_offre=null){
        //-----------------------------------security-------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED'))  {
            throw $this->createAccessDeniedException();
        }
        if($groupe_offre !== null){
            if ($this->getUser() !== $groupe_offre->getCreateur()){
                throw $this->createAccessDeniedException();
            }
        }
        //------------------------------------------------------------------------------
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
     * @param Groupe_offre $groupe_offre
     */
    private function editAndDeleteSecurity($groupe_offre){
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        /* ensure that the user is logged in
        *  and that the one is the owner
        */
        $user = $this->getUser();
        if ((!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) || ($groupe_offre->getCreateur() !== $user)) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------

    }
}
