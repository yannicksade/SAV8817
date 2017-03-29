<?php

namespace APM\MarketingDistribueBundle\Controller;

use APM\MarketingDistribueBundle\Entity\Conseiller_boutique;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Conseiller_boutique controller.
 *
 */
class Conseiller_boutiqueController extends Controller
{
    /**
     * Lists all conseiller_boutique entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $conseiller_boutiques = $em->getRepository('APMMarketingDistribueBundle:Conseiller_boutique')->findAll();

        return $this->render('APMMarketingDistribueBundle:conseiller_boutique:index.html.twig', array(
            'conseiller_boutiques' => $conseiller_boutiques,
        ));
    }

    /**
     * Creates a new conseiller_boutique entity.
     *
     */
    public function newAction(Request $request)
    {
        $conseiller_boutique = new Conseiller_boutique();
        $form = $this->createForm('APM\MarketingDistribueBundle\Form\Conseiller_boutiqueType', $conseiller_boutique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
     *
     */
    public function showAction(Conseiller_boutique $conseiller_boutique)
    {
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
     *
     */
    public function editAction(Request $request, Conseiller_boutique $conseiller_boutique)
    {
        $deleteForm = $this->createDeleteForm($conseiller_boutique);
        $editForm = $this->createForm('APM\MarketingDistribueBundle\Form\Conseiller_boutiqueType', $conseiller_boutique);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
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
     *
     */
    public function deleteAction(Request $request, Conseiller_boutique $conseiller_boutique)
    {
        $form = $this->createDeleteForm($conseiller_boutique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($conseiller_boutique);
            $em->flush();
        }

        return $this->redirectToRoute('apm_marketing_conseiller_boutique_index');
    }

    public function deleteFromListAction(Conseiller $object)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($object);
        $em->flush();

        return $this->redirectToRoute('apm_marketing_conseiller_boutique_index');
    }
}
