<?php

namespace APM\MarketingDistribueBundle\Controller;

use APM\MarketingDistribueBundle\Entity\Conseiller;
use APM\MarketingDistribueBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Conseiller controller.
 *
 */
class ConseillerController extends Controller
{
    /**
     * Liste tous les conseillers
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $this->listAndShowSecurity();
        $em = $this->getDoctrine()->getManager();
        $conseillers = $em->getRepository('APMMarketingDistribueBundle:conseiller')->findAll();
        return $this->render('APMMarketingDistribueBundle:conseiller:index.html.twig', array(
            'conseillers' => $conseillers,
        ));
    }

    private function listAndShowSecurity()
    {
        //-----------------------------------security-------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * Creates a new Conseiller entity.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $this->createSecurity();
        /** @var Conseiller $conseiller */
        $conseiller = TradeFactory::getTradeProvider("conseiller");
        $form = $this->createForm('APM\MarketingDistribueBundle\Form\ConseillerType', $conseiller);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->createSecurity();
            /** @var Utilisateur_avm $user */
            $user = $this->getUser();
            $conseiller->setUtilisateur($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($conseiller);
            $em->flush();

            return $this->redirectToRoute('apm_marketing_conseiller_show', array('id' => $conseiller->getId()));
        }

        return $this->render('APMMarketingDistribueBundle:conseiller:new.html.twig', array(
            'conseiller' => $conseiller,
            'form' => $form->createView(),
        ));
    }

    private function createSecurity()
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a CONSEILLER role
        $this->denyAccessUnlessGranted('ROLE_CONSEILLER', null, 'Unable to access this page!');
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') || !$user->isConseillerA1()) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * Finds and displays a Conseiller entity.
     * @param Conseiller $conseiller
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Conseiller $conseiller)
    {
        $this->listAndShowSecurity();

        $deleteForm = $this->createDeleteForm($conseiller);

        return $this->render('APMMarketingDistribueBundle:conseiller:show.html.twig', array(
            'conseiller' => $conseiller,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Conseiller entity.
     *
     * @param Conseiller $conseiller The Conseiller entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Conseiller $conseiller)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_marketing_conseiller_delete', array('id' => $conseiller->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Conseiller entity.
     * @param Request $request
     * @param Conseiller $conseiller
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Conseiller $conseiller)
    {
        $this->editAndDeleteSecurity($conseiller);
        $deleteForm = $this->createDeleteForm($conseiller);
        $editForm = $this->createForm('APM\MarketingDistribueBundle\Form\ConseillerType', $conseiller);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->editAndDeleteSecurity($conseiller);
            $em = $this->getDoctrine()->getManager();
            $em->persist($conseiller);
            $em->flush();

            return $this->redirectToRoute('apm_marketing_conseiller_show', array('id' => $conseiller->getId()));
        }

        return $this->render('APMMarketingDistribueBundle:conseiller:edit.html.twig', array(
            'conseiller' => $conseiller,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @param Conseiller $conseiller
     */
    private function editAndDeleteSecurity($conseiller)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a CONSEILLER role
        $this->denyAccessUnlessGranted('ROLE_CONSEILLER', null, 'Unable to access this page!');
        $user = $this->getUser();
        if ((!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) ||
            ($conseiller->getUtilisateur() !== $user)
        ) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * Deletes a Conseiller entity.
     * @param Request $request
     * @param Conseiller $conseiller
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Conseiller $conseiller)
    {
        $this->editAndDeleteSecurity($conseiller);
        $form = $this->createDeleteForm($conseiller);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->editAndDeleteSecurity($conseiller);
            $em = $this->getDoctrine()->getManager();
            $em->remove($conseiller);
            $em->flush();
        }

        return $this->redirectToRoute('apm_marketing_conseiller_index');
    }

    public function deleteFromListAction(Conseiller $conseiller)
    {
        $this->editAndDeleteSecurity($conseiller);
        $em = $this->getDoctrine()->getManager();
        $em->remove($conseiller);
        $em->flush();

        return $this->redirectToRoute('apm_marketing_conseiller_index');
    }
}
