<?php

namespace APM\MarketingDistribueBundle\Controller;

use APM\MarketingDistribueBundle\Entity\Commissionnement;
use APM\MarketingDistribueBundle\Entity\Conseiller_boutique;
use APM\MarketingDistribueBundle\Entity\Quota;
use APM\MarketingDistribueBundle\Factory\TradeFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Commissionnement controller.
 *
 */
class CommissionnementController extends Controller
{
    /**
     * Liste
     * @param Conseiller_boutique $conseiller_boutique
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Conseiller_boutique $conseiller_boutique)
    {
        $this->listAndShowSecurity($conseiller_boutique);
        $commissionnements =$conseiller_boutique->getCommissionnements();

        return $this->render('APMMarketingDistribueBundle:commissionnement:index.html.twig', array(
            'commissionnements' => $commissionnements,
        ));
    }

    /**
     * Créer un commissionnement pour consiller-boutique
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        //s'assurer en amont que les conditions requises au conseiller sont remplies pour créer un commissionnement
        // pour appeler une fonction callback pour cette vérification

        $this->createSecurity();
        /** @var Commissionnement $commissionnement */
        $commissionnement = TradeFactory::getTradeProvider("commissionnement");
        $form = $this->createForm('APM\MarketingDistribueBundle\Form\CommissionnementType', $commissionnement);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->createSecurity($data('conseiller_boutique'), $data('quota'));
            $em = $this->getDoctrine()->getManager();
            $em->persist($commissionnement);
            $em->flush();

            return $this->redirectToRoute('apm_marketing_commissionnement_show', array('id' => $commissionnement->getId()));
        }

        return $this->render('APMMarketingDistribueBundle:commissionnement:new.html.twig', array(
            'commissionnement' => $commissionnement,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Commissionnement entity.
     * @param Commissionnement $commissionnement
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Commissionnement $commissionnement)
    {

        $this->listAndShowSecurity($commissionnement->getConseillerBoutique());

        $deleteForm = $this->createDeleteForm($commissionnement);

        return $this->render('APMMarketingDistribueBundle:commissionnement:show.html.twig', array(
            'commissionnement' => $commissionnement,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Commissionnement entity.
     *
     * @param Commissionnement $commissionnement The Commissionnement entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Commissionnement $commissionnement)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_marketing_commissionnement_delete', array('id' => $commissionnement->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Commissionnement entity.
     * @param Request $request
     * @param Commissionnement $commissionnement
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Commissionnement $commissionnement)
    {
        $this->editAndDeleteSecurity($commissionnement);
        $deleteForm = $this->createDeleteForm($commissionnement);
        $editForm = $this->createForm('APM\MarketingDistribueBundle\Form\CommissionnementType', $commissionnement);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->editAndDeleteSecurity($commissionnement);
            $em = $this->getDoctrine()->getManager();
            $em->persist($commissionnement);
            $em->flush();

            return $this->redirectToRoute('apm_marketing_commissionnement_show', array('id' => $commissionnement->getId()));
        }

        return $this->render('APMMarketingDistribueBundle:commissionnement:edit.html.twig', array(
            'commissionnement' => $commissionnement,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Supprimer à partir d'un formulaire
     * @param Request $request
     * @param Commissionnement $commissionnement
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Commissionnement $commissionnement)
    {
        $this->editAndDeleteSecurity($commissionnement);
        $form = $this->createDeleteForm($commissionnement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->editAndDeleteSecurity($commissionnement);
            $em = $this->getDoctrine()->getManager();
            $em->remove($commissionnement);
            $em->flush();
        }

        return $this->redirectToRoute('apm_marketing_commissionnement_index', $commissionnement->getConseillerBoutique()->getId());
    }

    public function deleteFromListAction(Commissionnement $commissionnement)
    {
        $this->editAndDeleteSecurity($commissionnement);
        $em = $this->getDoctrine()->getManager();
        $em->remove($commissionnement);
        $em->flush();

        return $this->redirectToRoute('apm_marketing_commissionnement_index');
    }

    /**
     * @param Commissionnement $commissionnement
     */
    private function editAndDeleteSecurity($commissionnement){
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_NO_ACCESS', null, 'Unable to access this page!');
        /* ensure that the user is logged in  # granted even through remembering cookies
        *  and that the one is the owner
        */
        $user = $this->getUser();
        if ((!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) || ($commissionnement->getConseillerBoutique()->getConseiller()->getUtilisateur() !== $user)) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @param Conseiller_boutique $conseiller_boutique
     * @param Quota $quota
     */
    private function createSecurity($conseiller_boutique = null, $quota = null){
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have the required role
        $this->denyAccessUnlessGranted('ROLE_CONSEILLER', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        /* ensure that the user is logged in
        *  and that the one is the owner
         * Test d'identité!
        */
        //la boutique pourlaquelle le conseiller beneficie des commissionnement doit être la même qui offre le Quota
        $user = $this->getUser();
        if($conseiller_boutique && $quota) {
            if ($conseiller_boutique->getConseiller()->getUtilisateur() !== $user || $quota->getBoutiqueProprietaire() !== $conseiller_boutique->getBoutique()) {
                throw $this->createAccessDeniedException();
            }
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @param Conseiller_boutique $conseiller_boutique
     */
    private function listAndShowSecurity($conseiller_boutique){
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_CONSEILLER', null, 'Unable to access this page!');
        /* ensure that the user is logged in  # granted even through remembering cookies
        *  and that the one is the owner
        */
        $user = $this->getUser();
            if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED') || $conseiller_boutique->getConseiller()->getUtilisateur() !== $user) {
                throw $this->createAccessDeniedException();
            }
        //----------------------------------------------------------------------------------------
    }
}
