<?php

namespace APM\MarketingDistribueBundle\Controller;

use APM\MarketingDistribueBundle\Entity\Quota;
use APM\MarketingDistribueBundle\Factory\TradeFactory;
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
     *  Lister en fonction de la boutique ou des valeurs [name => valeur]
     * @param Boutique $boutique
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Boutique $boutique)
    {
        $this->listAndShowSecurity($boutique);
       $quotas = $boutique->getCommissionnements();
        return $this->render('APMMarketingDistribueBundle:quota:index.html.twig', array(
            'quotas' => $quotas,
        ));
    }

    /**
     * Creates a new Quota entity.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $this->createSecurity();

        /** @var Quota $quotum */
        $quotum = TradeFactory::getTradeProvider("quota");
        $form = $this->createForm('APM\MarketingDistribueBundle\Form\QuotaType', $quotum);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->createSecurity($form->getData()['boutique']);
            $em = $this->getDoctrine()->getManager();
            $em->persist($quotum);
            $em->flush();

            return $this->redirectToRoute('apm_marketing_quota_show', array('id' => $quotum->getId()));
        }

        return $this->render('APMMarketingDistribueBundle:quota:new.html.twig', array(
            'quotum' => $quotum,
            'form' => $form->createView(),
        ));
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

    /**
     * @param Boutique $boutique
     */
    private function listAndShowSecurity($boutique){
        //-----------------------------------security-----------------------------------------------------------
        // Unable to access the controller unless are the owner or you have the CONSEILLER role
        // Le Conseiller à le droit de lister tous les quotas
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();}
        $user = $this->getUser();
        $gerant = $boutique->getGerant()||$boutique->getProprietaire();
        $user === $gerant?: $this->denyAccessUnlessGranted('ROLE_CONSEILLER', null, 'Unable to access this page!');

        //------------------------------------------------------------------------------------------------------
    }

    /**
     * @param Quota $quotum
     */
    private function editAndDeleteSecurity($quotum){
        //---------------------------------security-----------------------------------------------
        // Unable to Edit or delete unless you are the owner
        $this->denyAccessUnlessGranted('ROLE_BOUTIQUE', null, 'Unable to access this page!');
        /* ensure that the user is logged in  # granted*/
        $user = $this->getUser();
        $gerant = $quotum->getBoutiqueProprietaire()->getGerant() || $quotum->getBoutiqueProprietaire()->getProprietaire();
        if ((!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) || ( $gerant!== $user)) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------

    }

    /**
     * @param Boutique $boutique
     */
    private function createSecurity($boutique = null){
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_BOUTIQUE', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        /* ensure that the user is logged in  # granted even through remembering cookies
        *  and that the one is the the manager of the shop
        */
        if($boutique) {
            $user = $this->getUser();
            $gerant = $boutique->getGerant() || $boutique->getProprietaire();
            if ($user !== $gerant) {
                throw $this->createAccessDeniedException();
            }
        }
        //----------------------------------------------------------------------------------------
    }
}
