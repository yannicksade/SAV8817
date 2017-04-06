<?php

namespace APM\VenteBundle\Controller;

use APM\VenteBundle\Entity\Offre;
use APM\VenteBundle\Entity\Remise;
use APM\VenteBundle\Factory\TradeFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Remise controller.
 *
 */
class RemiseController extends Controller
{

    /**
     * @param Offre $offre
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Offre $offre)
    {
        $this->listAndShowSecurity($offre);
        $remises = $offre->getRemises();
        return $this->render('APMVenteBundle:remise:index.html.twig', array(
            'remises' => $remises,
            'offre' =>$offre
        ));
    }

    /**
     * @param Request $request
     * @param Offre $offre
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request, Offre $offre)
    {
        $this->createSecurity($offre);
        /** @var Remise $remise */
        $remise = TradeFactory::getTradeProvider('remise');
        $form = $this->createForm('APM\VenteBundle\Form\RemiseType', $remise);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->createSecurity($offre);
            $remise->setOffre($offre);
            $em = $this->getDoctrine()->getManager();
            $em->persist($remise);
            $em->flush();

            return $this->redirectToRoute('apm_vente_remise_show', array('id' => $remise->getId()));
        }

        return $this->render('APMVenteBundle:remise:new.html.twig', array(
            'form' => $form->createView(),
            'remise' => $remise
        ));
    }

    /**
     * Finds and displays a Remise entity.
     * @param Remise $remise
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Remise $remise)
    {
        $this->listAndShowSecurity($remise->getOffre());

        $deleteForm = $this->createDeleteForm($remise);

        return $this->render('APMVenteBundle:remise:show.html.twig', array(
            'remise' => $remise,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Remise entity.
     *
     * @param Remise $remise The Remise entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Remise $remise)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_vente_remise_delete', array('id' => $remise->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Remise entity.
     * @param Request $request
     * @param Remise $remise
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Remise $remise)
    {
        $this->editAndDeleteSecurity($remise);
        $deleteForm = $this->createDeleteForm($remise);
        $editForm = $this->createForm('APM\VenteBundle\Form\RemiseType', $remise);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->editAndDeleteSecurity($remise);
            $em = $this->getDoctrine()->getManager();
            $em->persist($remise);
            $em->flush();

            return $this->redirectToRoute('apm_vente_remise_show', array('id' => $remise->getId()));
        }

        return $this->render('APMVenteBundle:remise:edit.html.twig', array(
            'remise' => $remise,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Remise entity.
     * @param Request $request
     * @param Remise $remise
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Remise $remise)
    {
        $this->editAndDeleteSecurity($remise);

        $form = $this->createDeleteForm($remise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $this->editAndDeleteSecurity($remise);
            $em = $this->getDoctrine()->getManager();
            $em->remove($remise);
            $em->flush();
        }

        return $this->redirectToRoute('apm_vente_remise_index', ['id' =>$remise->getOffre()->getId()]);
    }

    public function deleteFromListAction(Remise $remise)
    {
        $this->editAndDeleteSecurity($remise);

        $em = $this->getDoctrine()->getManager();
        $em->remove($object);
        $em->flush();

        return $this->redirectToRoute('apm_vente_remise_index',['id' =>$remise->getOffre()->getId()]);
    }


    /**
     * @param Offre $offre
     */
    private function listAndShowSecurity($offre){
        //-----------------------------------security-------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_BOUTIQUE', null, 'Unable to access this page!');
        $user = $this->getUser();
        $vendeur = $offre->getVendeur();
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED') || $user !== $vendeur)  {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @param Offre $offre
     */
    private function createSecurity($offre = null){
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_BOUTIQUE', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();}
                $user = $this->getUser();
        if($offre) {
            $vendeur = $offre->getVendeur();
            if ($user !== $vendeur) {
                throw $this->createAccessDeniedException();
            }
        }
    }

    /**
     * @param Remise $remise
     */
    private function editAndDeleteSecurity($remise){
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_BOUTIQUE', null, 'Unable to access this page!');
        /* ensure that the user is logged in  # granted even through remembering cookies
        *  and that the one is the owner
        */
        $user = $this->getUser();
        $vendeur = $remise->getOffre()->getVendeur();
        if ((!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) || ($vendeur!== $user)) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------

    }
}
