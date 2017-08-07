<?php

namespace APM\MarketingDistribueBundle\Controller;

use APM\MarketingDistribueBundle\Entity\Quota;
use APM\MarketingDistribueBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Boutique;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


/**
 * Quota controller.
 *
 */
class QuotaController extends Controller
{
    /**
     *
     *  Liste les commissions de la boutique
     * @param Boutique $boutique
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Boutique $boutique)
    {
        $this->listAndShowSecurity($boutique);
        $quotas = $boutique->getCommissionnements();
        return $this->render('APMMarketingDistribueBundle:quota:index_old.html.twig', array(
            'quotas' => $quotas,
            'boutique' => $boutique,
        ));
    }

    /**
     * @param Boutique $boutique
     */
    private function listAndShowSecurity($boutique = null)
    {
        //-----------------------------------security-----------------------------------------------------------
        // Unable to access the controller unless are the owner or you have the CONSEILLER role
        // Le Conseiller et la boutique à le droit de lister tous les quotas
        $this->denyAccessUnlessGranted(['ROLE_CONSEILLER', 'ROLE_BOUTIQUE'], null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $conseiller = $user->getProfileConseiller();
        $gerant = null;
        $proprietaire = null;
        if ($boutique) {
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
        }
        if (null === $conseiller && $user !== $gerant && $user !== $proprietaire) throw $this->createAccessDeniedException();

        //------------------------------------------------------------------------------------------------------
    }

    /**
     * Creates a new Quota entity.
     * @param Request $request
     * @param Boutique $boutique
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request, Boutique $boutique)
    {
        $this->createSecurity($boutique);

        /** @var Quota $quotum */
        $quotum = TradeFactory::getTradeProvider("quota");
        $form = $this->createForm('APM\MarketingDistribueBundle\Form\QuotaType', $quotum);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->createSecurity($boutique);
            $quotum->setBoutiqueProprietaire($boutique);
            $em = $this->getDoctrine()->getManager();
            $em->persist($quotum);
            $em->flush();

            return $this->redirectToRoute('apm_marketing_quota_show', array('id' => $quotum->getId()));
        }

        return $this->render('APMMarketingDistribueBundle:quota:new.html.twig', array(
            'quotum' => $quotum,
            'form' => $form->createView(),
            'boutique' => $boutique,
        ));
    }

    /**
     * @param Boutique $boutique
     */
    private function createSecurity($boutique = null)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_BOUTIQUE', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        if ($boutique) {
            $user = $this->getUser();
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
            if ($user !== $gerant && $user !== $proprietaire) {
                throw $this->createAccessDeniedException();
            }
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * Finds and displays a Quota entity.
     * @param Quota $quotum
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Quota $quotum)
    {
        $this->listAndShowSecurity($quotum->getBoutiqueProprietaire());
        $deleteForm = $this->createDeleteForm($quotum);

        return $this->render('APMMarketingDistribueBundle:quota:show.html.twig', array(
            'quotum' => $quotum,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Quota entity.
     *
     * @param Quota $quotum The Quota entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Quota $quotum)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_marketing_quota_delete', array('id' => $quotum->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Quota entity.
     * @param Request $request
     * @param Quota $quotum
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Quota $quotum)
    {
        $this->editAndDeleteSecurity($quotum);

        $deleteForm = $this->createDeleteForm($quotum);
        $editForm = $this->createForm('APM\MarketingDistribueBundle\Form\QuotaType', $quotum);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->editAndDeleteSecurity($quotum);
            $em = $this->getDoctrine()->getManager();
            $em->persist($quotum);
            $em->flush();

            return $this->redirectToRoute('apm_marketing_quota_show', array('id' => $quotum->getId()));
        }

        return $this->render('APMMarketingDistribueBundle:quota:edit.html.twig', array(
            'quotum' => $quotum,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @param Quota $quotum
     */
    private function editAndDeleteSecurity($quotum)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to Edit or delete unless you are the owner
        $this->denyAccessUnlessGranted('ROLE_BOUTIQUE', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $user = $this->getUser();
        $gerant = $quotum->getBoutiqueProprietaire()->getGerant();
        $proprietaire = $quotum->getBoutiqueProprietaire()->getProprietaire();
        if ($gerant !== $user && $user !== $proprietaire) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------

    }

    /**
     * Deletes a Quota entity.
     * @param Request $request
     * @param Quota $quotum
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Quota $quotum)
    {
        $this->editAndDeleteSecurity($quotum);

        $form = $this->createDeleteForm($quotum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->editAndDeleteSecurity($quotum);
            $em = $this->getDoctrine()->getManager();
            $em->remove($quotum);
            $em->flush();
        }

        return $this->redirectToRoute('apm_marketing_quota_index', ['id' => $quotum->getBoutiqueProprietaire()->getId()]);
    }

    public function deleteFromListAction(Quota $quotum)
    {
        $this->editAndDeleteSecurity($quotum);

        $em = $this->getDoctrine()->getManager();
        $em->remove($quotum);
        $em->flush();

        return $this->redirectToRoute('apm_marketing_quota_index', ['id' => $quotum->getBoutiqueProprietaire()->getId()]);
    }
}
