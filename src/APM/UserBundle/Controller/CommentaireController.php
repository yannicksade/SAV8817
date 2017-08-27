<?php

namespace APM\UserBundle\Controller;

use APM\UserBundle\Entity\Commentaire;
use APM\UserBundle\Factory\TradeFactory;
use APM\VenteBundle\Entity\Offre;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Commentaire controller.
 * Tout utilisateur peut éditer, modifier ou supprimer des commentaires sur n'importe qu'elle offre; mais seul le proprietaire
 * de l'offre peut les publier
 *
 */
class CommentaireController extends Controller
{
    /**
     * Liste les commentaires faits sur une offre
     * un commentaire sur une offre pourrait être publié ou non
     * Tant q'un commentaire n'est pas publié, il n'est accessible qu'à celui qui l'a poster et au propriétaire(et gerant) de l'offre
     *
     * @param Offre $offre
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Offre $offre)
    {
        $this->listAndShowSecurity();
        $vendeur = $offre->getVendeur();
        $boutique = $offre->getBoutique();
        $gerant = null;
        $proprietaire = null;
        $commentaires = null;
        if ($boutique) {
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
        }
        $comments = $offre->getCommentaires();
        $commentaires = $comments;
        $user = $this->getUser();
        if ($user !== $vendeur && $user !== $gerant && $user !== $proprietaire) {
            $commentaires = null;
            /** @var Commentaire $commentaire */
            foreach ($comments as $commentaire) { //presenter uniquement les commentaires publiés au publique
                if ($commentaire->isPubliable() || $commentaire->getUtilisateur() === $user) {
                    $commentaires [] = $commentaire;
                }
            }
        }

        return $this->render('APMUserBundle:commentaire:index.html.twig', array(
            'commentaires' => $commentaires,
            'offre' => $offre,
        ));
    }

    /**
     *
     * @internal param bool $access
     */
    private function listAndShowSecurity()
    {
        //-----------------------------------security-------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * Creates a new Commentaire entity.
     * @param Request $request
     * @param Offre $offre
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request, Offre $offre)
    {
        $this->createSecurity($offre);
        /** @var Commentaire $commentaire */
        $commentaire = TradeFactory::getTradeProvider("commentaire");
        $form = $this->createForm('APM\UserBundle\Form\CommentaireType', $commentaire);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->createSecurity($offre);
            $commentaire->setUtilisateur($this->getUser());
            $commentaire->setOffre($offre);
            $em = $this->getDoctrine()->getManager();
            $em->persist($commentaire);
            $em->flush();

            return $this->redirectToRoute('apm_user_commentaire_show', array('id' => $commentaire->getId()));
        }

        return $this->render('APMUserBundle:commentaire:new.html.twig', array(
            'commentaire' => $commentaire,
            'offre' => $offre,
            'form' => $form->createView(),
        ));
    }

    /**
     * @param Offre $offre
     */
    private function createSecurity($offre)
    {
        //---------------------------------security-----------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        if (!$offre->getPubliable()) throw $this->createAccessDeniedException();
        //----------------------------------------------------------------------------------------
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
     * @param Commentaire $commentaire
     */
    private function editAndDeleteSecurity($commentaire)
    {
        //---------------------------------security-----------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        /* ensure that the user is logged in
        *  and that the one is the author
        */
        $user = $this->getUser();
        if ((!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) || ($commentaire->getUtilisateur() !== $user)) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------

    }

    // pour soumettre un commentaire il faut que l'offre soit publique

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

    public function deleteFromListAction(Commentaire $commentaire)
    {
        $this->editAndDeleteSecurity($commentaire);
        $em = $this->getDoctrine()->getManager();
        $em->remove($commentaire);
        $em->flush();

        return $this->redirectToRoute('apm_user_commentaire_index');
    }

}
