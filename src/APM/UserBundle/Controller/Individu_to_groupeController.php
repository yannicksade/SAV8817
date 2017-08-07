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
     * Liste un ou (tous) les groupes de son proprietaire contenant des individus
     *
     * @param Groupe_relationnel $groupe_relationnel
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Groupe_relationnel $groupe_relationnel = null)
    {
        $this->listeAndShowSecurity($groupe_relationnel);
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $individu_to_groupes = null;
        if ($groupe_relationnel) {//liste la relation donnée par le groupe
            $individu_to_groupes [] = [
                "groupe" => $groupe_relationnel,
                "personnes" => $groupe_relationnel->getGroupeIndividus()];
        } else {//liste toutes les relations de groupe d'un individu
            $groupes = $user->getGroupesProprietaire();
            /** @var Groupe_relationnel $groupe */
            foreach ($groupes as $groupe) {
                $individu_to_groupes [] = array(
                    "groupe" => $groupe,
                    "personnes" => $groupe->getGroupeIndividus()
                );
            }
            //----- Ajout des groupes de conversation : groupes auxquels appartient l'utilisateur ---------------------
            $individu_groupes = $user->getIndividuGroupes();
            foreach ($individu_groupes as $individu_groupe) {
                /** @var Groupe_relationnel $groupe_relationnel */
                $groupe_relationnel = $individu_groupe->getGroupeRelationnel();
                if ($groupe_relationnel->isConversationalGroup() && $user != $groupe_relationnel->getProprietaire())
                    $individu_to_groupes [] = array(
                        'groupe' => $groupe_relationnel,
                        'personnes' => $individu_groupe);
            }
            //---------------------------------------------------------------------------------------------------------
        }
        return $this->render('APMUserBundle:individu_to_groupe:index_old.html.twig', [
            'individu_to_groupes' => $individu_to_groupes,
                'groupe' => $groupe_relationnel,

            ]
        );
    }
    //liste les offres d'une transaction de produit

    /**
     * @param Groupe_relationnel $groupe
     */
    private function listeAndShowSecurity($groupe)
    {
        //---------------------------------security-----------------------------------------------
        // Liste tous les groupes auxquels l'utilisateur appartient
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw $this->createAccessDeniedException();
        }
        if ($groupe) {
            $isGroupMember = false;
            /** @var Utilisateur_avm $user */
            $user = $this->getUser();
            $groupe_individus = $groupe->getGroupeIndividus();
            /** @var Individu_to_groupe $groupe_individu */
            foreach ($groupe_individus as $groupe_individu) {
                if ($groupe_individu->getIndividu() === $user) $isGroupMember = true;
            }

            if ($user !== $groupe->getProprietaire() && !$isGroupMember) {
                throw $this->createAccessDeniedException();
            }
        }
        //-----------------------------------------------------------------------------------------
    }

    public function listUsersAction(Groupe_relationnel $groupe_relationnel)
    {
        $this->listeAndShowSecurity($groupe_relationnel);
        $individu_to_groupes = $groupe_relationnel->getGroupeIndividus();
        $users = null;

        /** @var Individu_to_groupe $individu_to_groupe */
        foreach ($individu_to_groupes as $individu_to_groupe) {
            $users [] = $individu_to_groupe->getIndividu();
        }
        return $this->render('APMUserBundle:Utilisateur_avm:index_old.html.twig', array(
            'users' => $users,
            'groupe' => $groupe_relationnel,
        ));
    }

    public function indexConversationalGroupAction()
    {
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $individu_groupes = $user->getIndividuGroupes();
        $individu_to_groupes = null;
        /** @var Individu_to_groupe $individu_groupe */
        foreach ($individu_groupes as $individu_groupe) {
            if ($individu_groupe->getGroupeRelationnel()->isConversationalGroup())
                $individu_to_groupes [] = array(
                    'groupe' => $individu_groupe->getGroupeRelationnel(),
                    'personnes' => $individu_groupe);
        }
        return $this->render('APMUserBundle:individu_to_groupe:index_old.html.twig',
            ['individu_to_groupes' => $individu_to_groupes]
        );
    }

    /**
     *  Affecter l'utilisateur à un groupe
     * @param Request $request
     * @param Groupe_relationnel $groupe_relationnel
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request, Groupe_relationnel $groupe_relationnel = null)
    {
        $this->createSecurity($groupe_relationnel);
        /** @var Individu_to_groupe $individu_to_groupe */
        $individu_to_groupe = TradeFactory::getTradeProvider("individu_to_groupe");
        $form = $this->createForm('APM\UserBundle\Form\Individu_to_groupeType', $individu_to_groupe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($groupe_relationnel) {
                $this->createSecurity($groupe_relationnel, $form->get('individu')->getData());
                $individu_to_groupe->setGroupeRelationnel($groupe_relationnel);
            } else {
                $this->createSecurity($form->get('groupeRelationnel')->getData(), $form->get('individu')->getData());
            }


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
     * @param Groupe_relationnel $groupe
     * @param Utilisateur_avm|null $individu
     */
    private function createSecurity($groupe = null, $individu = null)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        if ($groupe) {//se rassurer que le groupe relationnel appartient bien à l'utilisateur
            $user = $this->getUser();
            if ($individu) { //Evite la duplication de personne dans un meme groupe
                $oldIndividu = null;
                $em = $this->getDoctrine()->getManager();
                /** @var Individu_to_groupe $oldIndividu */
                $oldIndividu = $em->getRepository('APMUserBundle:Individu_to_groupe')
                    ->findOneBy(['individu' => $individu]);
                $oldGroupe = $oldIndividu->getGroupeRelationnel();

                if ($user !== $groupe->getProprietaire() || null !== $oldIndividu && $groupe === $oldGroupe) {
                    throw $this->createAccessDeniedException();
                }
            }
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * Finds and displays a Individu_to_groupe entity.
     * @param Individu_to_groupe $individu_to_groupe
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Individu_to_groupe $individu_to_groupe)
    {
        $this->listeAndShowSecurity($individu_to_groupe->getGroupeRelationnel());
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
     * L'utilisateur doit être propriétaire du groupe
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
}
