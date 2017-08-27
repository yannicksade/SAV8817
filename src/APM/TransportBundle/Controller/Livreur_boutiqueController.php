<?php

namespace APM\TransportBundle\Controller;

use APM\TransportBundle\Entity\Livreur_boutique;
use APM\TransportBundle\Entity\Profile_transporteur;
use APM\TransportBundle\Factory\TradeFactory;
use APM\VenteBundle\Entity\Boutique;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Livreur_boutique controller.
 *
 */
class Livreur_boutiqueController extends Controller
{
    /**
     * Tout le monde peut Lister les livreurs d'une boutique
     * un livreur peut appartenir à plusieurs boutiques
     *
     * @param Boutique $boutique
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Boutique $boutique)
    {
        $this->listeAndShowSecurity();
        $livreurs_Empruntes = $boutique->getLivreurs();//livreurs étrangers: empruntés
        $livreurs_boutiques = $boutique->getLivreurBoutiques();//livreurs crées par la boutique

        return $this->render('APMTransportBundle:livreur_boutique:index.html.twig', array(
            'livreurs_boutiques' => $livreurs_boutiques,
            'livreurs_Empruntes' => $livreurs_Empruntes,
            'boutique' => $boutique,
        ));
    }

    private function listeAndShowSecurity()
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have the role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    // les boutiques sont responsables de la création des transporteurs
    public function newAction(Request $request, Boutique $boutique)
    {
        $this->createSecurity($boutique);
        /** @var Livreur_boutique $livreur_boutique */
        $livreur_boutique = TradeFactory::getTradeProvider("livreur_boutique");
        /** @var Profile_transporteur $transporteur */
        $transporteur = TradeFactory::getTradeProvider("transporteur");
        $livreur_boutique->setTransporteur($transporteur);
        $form = $this->createForm('APM\TransportBundle\Form\Livreur_boutiqueType', $livreur_boutique);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->createSecurity($boutique);
            $livreur_boutique->setBoutiqueProprietaire($boutique);
            $transporteur->setLivreurBoutique($livreur_boutique);
            $em = $this->getDoctrine()->getManager();
            $em->persist($livreur_boutique);
            $em->flush();

            return $this->redirectToRoute('apm_transport_livreur_boutique_show', array('id' => $livreur_boutique->getId()));
        }

        return $this->render('APMTransportBundle:livreur_boutique:new.html.twig', array(
            'livreur_boutique' => $livreur_boutique,
            'boutique' => $boutique,
            'form' => $form->createView(),
        ));
    }

    /**
     * uniquement les gerants et proprietaires de boutique sont autorisées à créer des livreurs boutique
     * @param Boutique $boutique
     */
    private function createSecurity($boutique)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_BOUTIQUE', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $user = $this->getUser();
        $gerant = $boutique->getGerant();
        $proprietaire = $boutique->getProprietaire();
        if ($user !== $gerant && $user !== $proprietaire) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * Tout le monde peut voir les détail d'un livreur boutique
     * @param Livreur_boutique $livreur_boutique
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Livreur_boutique $livreur_boutique)
    {
        $this->listeAndShowSecurity();

        $deleteForm = $this->createDeleteForm($livreur_boutique);

        return $this->render('APMTransportBundle:livreur_boutique:show.html.twig', array(
            'livreur_boutique' => $livreur_boutique,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Livreur_boutique entity.
     *
     * @param Livreur_boutique $livreur_boutique The Livreur_boutique entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private
    function createDeleteForm(Livreur_boutique $livreur_boutique)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_transport_livreur_boutique_delete', array('id' => $livreur_boutique->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Livreur_boutique entity.
     * @param Request $request
     * @param Livreur_boutique $livreur_boutique
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public
    function editAction(Request $request, Livreur_boutique $livreur_boutique)
    {
        $this->editAndDeleteSecurity($livreur_boutique);
        $deleteForm = $this->createDeleteForm($livreur_boutique);
        $editForm = $this->createForm('APM\TransportBundle\Form\Livreur_boutiqueType', $livreur_boutique);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->editAndDeleteSecurity($livreur_boutique);
            $em = $this->getDoctrine()->getManager();
            $em->persist($livreur_boutique);
            $em->flush();

            return $this->redirectToRoute('apm_transport_livreur_boutique_show', array('id' => $livreur_boutique->getId()));
        }

        return $this->render('APMTransportBundle:livreur_boutique:edit.html.twig', array(
            'livreur_boutique' => $livreur_boutique,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @param Livreur_boutique $livreur_boutique
     */
    private function editAndDeleteSecurity($livreur_boutique)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have
        $this->denyAccessUnlessGranted('ROLE_BOUTIQUE', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $user = $this->getUser();
        $boutique = $livreur_boutique->getBoutiqueProprietaire();
        $gerant = $boutique->getGerant();
        $proprietaire = $boutique->getProprietaire();
        if ($user !== $gerant && $user !== $proprietaire) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * Deletes a Livreur_boutique entity.
     * @param Request $request
     * @param Livreur_boutique $livreur_boutique
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public
    function deleteAction(Request $request, Livreur_boutique $livreur_boutique)
    {
        $this->editAndDeleteSecurity($livreur_boutique);

        $form = $this->createDeleteForm($livreur_boutique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->editAndDeleteSecurity($livreur_boutique);
            $em = $this->getDoctrine()->getManager();
            $em->remove($livreur_boutique);
            $em->flush();
        }

        return $this->redirectToRoute('apm_transport_livreur_boutique_index', ['id' => $livreur_boutique->getBoutiqueProprietaire()->getId()]);
    }

    public
    function deleteFromListAction(Livreur_boutique $livreur_boutique)
    {

        $this->editAndDeleteSecurity($livreur_boutique);
        $em = $this->getDoctrine()->getManager();
        $em->remove($livreur_boutique);
        $em->flush();

        return $this->redirectToRoute('apm_transport_livreur_boutique_index', ['id' => $livreur_boutique->getBoutiqueProprietaire()->getId()]);
    }


}
