<?php

namespace APM\TransportBundle\Controller;

use APM\TransportBundle\Entity\Livraison;
use APM\TransportBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Boutique;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


/**
 * Livraison controller.
 *
 */
class LivraisonController extends Controller
{
    /**
     * Liste les livraison d'un utilisateur
     * @param Boutique $boutique
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Boutique $boutique =  null)
    {
        $this->listeAndShowSecurity($boutique);
        if(null !== $boutique) {
            $livraisons = $boutique->getLivraisons();
        }else{
            /** @var Utilisateur_avm $user */
            $user = $this->getUser();
            $livraisons = $user->getLivraisons();
        }
        return $this->render('APMTransportBundle:livraison:index.html.twig', array(
            'livraisons' => $livraisons,
        ));
    }

    /**
     * @param Boutique $boutique
     */
    private function listeAndShowSecurity($boutique)
    {
        //-----------------------------------security-------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();}
        if(null !== $boutique) {
            $user = $this->getUser();
            $gerant = $boutique->getGerant() || $boutique->getProprietaire();
            if($user !== $gerant){
                throw $this->createAccessDeniedException();
            }
        }
        //------------------------------------------------------------------------------------------
    }

    /**
     * Creates a new Livraison entity.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $this->createSecurity();
        /** @var Livraison $livraison */
        $livraison = TradeFactory::getTradeProvider("livraison");
        $form = $this->createForm('APM\TransportBundle\Form\LivraisonType', $livraison);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->createSecurity($form->getData()['boutique']);
            $livraison->setUtilisateur($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($livraison);
            $em->flush();

            return $this->redirectToRoute('apm_transport_livraison_show', array('id' => $livraison->getId()));
        }

        return $this->render('APMTransportBundle:livraison:new.html.twig', array(
            'livraison' => $livraison,
            'form' => $form->createView(),
        ));
    }

    /**
     * @param Boutique $boutique
     * Verifie si la boutique appartient à son proprietaire ou le gerant
     */
    private function createSecurity($boutique = null)
    {
        //--------security: verifie si l'utilisateur courant est le gerant de la boutique qui cree la livraison---------
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();}
        if(null !== $boutique) {
            $user = $this->getUser();
            $gerant = $boutique->getGerant() || $boutique->getProprietaire();
            if($user !== $gerant){
                throw $this->createAccessDeniedException();
            }
        }
        //--------------------------------------------------------------------------------------------------------------
    }

    /**
     * voir un livraison
     * @param Livraison $livraison
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Livraison $livraison)
    {
        $this->listeAndShowSecurity($livraison->getBoutique());

        $deleteForm = $this->createDeleteForm($livraison);

        return $this->render('APMTransportBundle:livraison:show.html.twig', array(
            'livraison' => $livraison,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Livraison entity.
     *
     * @param Livraison $livraison The Livraison entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Livraison $livraison)
    {
        $this->editAndDeleteSecurity($livraison);
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_transport_livraison_delete', array('id' => $livraison->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Livraison entity.
     * @param Request $request
     * @param Livraison $livraison
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Livraison $livraison)
    {

        $this->editAndDeleteSecurity($livraison);

        $deleteForm = $this->createDeleteForm($livraison);
        $editForm = $this->createForm('APM\TransportBundle\Form\LivraisonType', $livraison);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->editAndDeleteSecurity($livraison);
            $em = $this->getDoctrine()->getManager();
            $em->persist($livraison);
            $em->flush();

            return $this->redirectToRoute('apm_transport_livraison_show', array('id' => $livraison->getId()));
        }

        return $this->render('APMTransportBundle:livraison:edit.html.twig', array(
            'livraison' => $livraison,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @param Livraison $livraison
     */
    private function editAndDeleteSecurity($livraison)
    {
        //-----------------------------------security : au cas ou il s'agirait d'une boutique vérifier le droit de l'utilisateur ------------------
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();}
            $boutique = $livraison->getBoutique();
        if(null !== $boutique) {
            $user = $this->getUser();
            $gerant = $boutique->getGerant() || $boutique->getProprietaire();
            if($user !== $gerant){
                throw $this->createAccessDeniedException();
            }
        }
        //-----------------------------------------------------------------------------------------------------------------------------------------------

    }

    /**
     * Deletes a Livraison entity.
     * @param Request $request
     * @param Livraison $livraison
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Livraison $livraison)
    {
        $this->editAndDeleteSecurity($livraison);

        $form = $this->createDeleteForm($livraison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->editAndDeleteSecurity($livraison);
            $em = $this->getDoctrine()->getManager();
            $em->remove($livraison);
            $em->flush();
        }

        return $this->redirectToRoute('apm_transport_livraison_index');
    }

    public function deleteFromListAction(Livraison $livraison)
    {
        $this->editAndDeleteSecurity($livraison);
        $em = $this->getDoctrine()->getManager();
        $em->remove($livraison);
        $em->flush();

        return $this->redirectToRoute('apm_transport_livraison_index');
    }
}
