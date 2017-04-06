<?php

namespace APM\MarketingDistribueBundle\Controller;


use APM\MarketingDistribueBundle\Entity\Conseiller;
use APM\MarketingDistribueBundle\Entity\Conseiller_boutique;
use APM\MarketingDistribueBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Conseiller_boutique controller.
 *
 */
class Conseiller_boutiqueController extends Controller
{
    /**
     * Liste les boutique du conseiller
     * @return \Symfony\Component\HttpFoundation\Response Lister les boutique du conseiller
     *
     * Lister les boutique du conseiller
     */
    public function indexAction()
    {
        $this->listAndShowSecurity();

        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $boutiques = $user->getProfileConseiller()->getConseillerBoutiques();
        return $this->render('APMMarketingDistribueBundle:conseiller_boutique:index.html.twig', array('conseiller_boutiques' => $boutiques,
        ));
    }

    /**
     * Creates a new conseiller_boutique entity.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $this->createSecurity();
        /** @var Conseiller_boutique $conseiller_boutique */
        $conseiller_boutique = TradeFactory::getTradeProvider("conseiller_boutique");
        $form = $this->createForm('APM\MarketingDistribueBundle\Form\Conseiller_boutiqueType', $conseiller_boutique);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->createSecurity($form->getData()['conseiller']);
            $em = $this->getDoctrine()->getManager();
            $em->persist($conseiller_boutique);
            $em->flush();

            return $this->redirectToRoute('apm_marketing_conseiller_boutique_show', array('id' => $conseiller_boutique->getId()));
        }

        return $this->render('APMMarketingDistribueBundle:conseiller_boutique:new.html.twig', array(
            'conseiller_boutique' => $conseiller_boutique,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a conseiller_boutique entity.
     * @param Conseiller_boutique $conseiller_boutique
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Conseiller_boutique $conseiller_boutique)
    {
        $this->listAndShowSecurity($conseiller_boutique);

        $deleteForm = $this->createDeleteForm($conseiller_boutique);

        return $this->render('APMMarketingDistribueBundle:conseiller_boutique:show.html.twig', array(
            'conseiller_boutique' => $conseiller_boutique,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a conseiller_boutique entity.
     *
     * @param Conseiller_boutique $conseiller_boutique The conseiller_boutique entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Conseiller_boutique $conseiller_boutique)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_marketing_conseiller_boutique_delete', array('id' => $conseiller_boutique->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing conseiller_boutique entity.
     * @param Request $request
     * @param Conseiller_boutique $conseiller_boutique
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Conseiller_boutique $conseiller_boutique)
    {
       $this->editAndDeleteSecurity($conseiller_boutique);

        $deleteForm = $this->createDeleteForm($conseiller_boutique);
        $editForm = $this->createForm('APM\MarketingDistribueBundle\Form\Conseiller_boutiqueType', $conseiller_boutique);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->editAndDeleteSecurity($conseiller_boutique);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('apm_marketing_conseiller_boutique_show', array('id' => $conseiller_boutique->getId()));
        }

        return $this->render('APMMarketingDistribueBundle:conseiller_boutique:edit.html.twig', array(
            'conseiller_boutique' => $conseiller_boutique,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a conseiller_boutique entity.
     * @param Request $request
     * @param Conseiller_boutique $conseiller_boutique
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Conseiller_boutique $conseiller_boutique)
    {
        $this->editAndDeleteSecurity($conseiller_boutique);

        $form = $this->createDeleteForm($conseiller_boutique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->editAndDeleteSecurity($conseiller_boutique);
            $em = $this->getDoctrine()->getManager();
            $em->remove($conseiller_boutique);
            $em->flush();
        }

        return $this->redirectToRoute('apm_marketing_conseiller_boutique_index');
    }

    public function deleteFromListAction(Conseiller_boutique $conseiller_boutique)
    {
        $this->editAndDeleteSecurity($conseiller_boutique);

        $em = $this->getDoctrine()->getManager();
        $em->remove($conseiller_boutique);
        $em->flush();

        return $this->redirectToRoute('apm_marketing_conseiller_boutique_index');
    }

    /**
     * @param Conseiller $conseiller
     */
    private function createSecurity($conseiller = null){
        //---------------------------------security-----------------------------------------------
        // Vérifier que l'utilisateur courant est bel et bien le conseiller
        $this->denyAccessUnlessGranted('ROLE_CONSEILLER', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        /* ensure that the user is logged in  # granted even through remembering cookies
        *  and that the one is the owner
        */
        if($conseiller) {
            $user = $this->getUser();
            if ($user !== $conseiller->getUtilisateur()){
                throw $this->createAccessDeniedException();
            }
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @param Conseiller_boutique $conseiller_boutique
     */
    private function editAndDeleteSecurity($conseiller_boutique)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a CONSEILLER role
        $this->denyAccessUnlessGranted('ROLE_CONSEILLER', null, 'Unable to access this page!');
        /* ensure that the user is logged in  # granted even through remembering cookies
        *  and that the one is the owner
        */
        $user = $this->getUser();
        if ((!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) || ($conseiller_boutique->getConseiller()->getUtilisateur() !== $user)) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------

    }

    /**
     * @param Conseiller_boutique $conseiller_boutique
     */
    private function listAndShowSecurity($conseiller_boutique=null){
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a CONSEILLER or BOUTIQUE role
        $this->denyAccessUnlessGranted(['ROLE_CONSEILLER', 'ROLE_BOUTIQUE'], null, 'Unable to access this page!');
        /* ensure that the user is logged in  # granted even through remembering cookies
        *  and that the one is the owner
        */
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')){
            throw $this->createAccessDeniedException();
        }
        //Pour afficher les details des boutique affiliés
        if(null !== $conseiller_boutique){
                $user = $this->getUser();
              if($conseiller_boutique->getConseiller()->getUtilisateur() !== $user){
                  throw $this->createAccessDeniedException();
                }
        }
        //----------------------------------------------------------------------------------------
    }
}
