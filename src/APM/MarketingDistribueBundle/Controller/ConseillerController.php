<?php

namespace APM\MarketingDistribueBundle\Controller;

use APM\MarketingDistribueBundle\Entity\Conseiller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Conseiller controller.
 *
 */
class ConseillerController extends Controller
{
    /**
     * Lists all Conseiller entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $conseillers = $em->getRepository('APMMarketingDistribueBundle:Conseiller')->findAll();

        return $this->render('APMMarketingDistribueBundle:conseiller:index.html.twig', array(
            'conseillers' => $conseillers,
        ));
    }

    /**
     * Creates a new Conseiller entity.
     *
     */
    public function newAction(Request $request)
    {
        $conseiller = new Conseiller();
        $form = $this->createForm('APM\MarketingDistribueBundle\Form\ConseillerType', $conseiller);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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

    /**
     * Finds and displays a Conseiller entity.
     *
     */
    public function showAction(Conseiller $conseiller)
    {
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
     *
     */
    public function editAction(Request $request, Conseiller $conseiller)
    {
        $deleteForm = $this->createDeleteForm($conseiller);
        $editForm = $this->createForm('APM\MarketingDistribueBundle\Form\ConseillerType', $conseiller);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
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
     * Deletes a Conseiller entity.
     *
     */
    public function deleteAction(Request $request, Conseiller $conseiller)
    {
        $form = $this->createDeleteForm($conseiller);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($conseiller);
            $em->flush();
        }

        return $this->redirectToRoute('apm_marketing_conseiller_index');
    }

    public function deleteFromListAction(Conseiller $object)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($object);
        $em->flush();

        return $this->redirectToRoute('apm_marketing_conseiller_index');
    }
}
