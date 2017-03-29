<?php

namespace APM\AchatBundle\Controller;

use APM\AchatBundle\Entity\Groupe_offre;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Groupe_offre controller.
 * @UniqueEntity(fields = "code", message="Ce code existe deja!")
 */
class Groupe_offreController extends Controller
{
    /**
     * Lists all Groupe_offre entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $groupe_offres = $em->getRepository('APMAchatBundle:Groupe_offre')->findAll();

        return $this->render('APMAchatBundle:groupe_offre:index.html.twig', array(
            'groupe_offres' => $groupe_offres,
        ));
    }

    /**
     * Creates a new Groupe_offre entity.
     *
     */
    public function newAction(Request $request)
    {
        $groupe_offre = new Groupe_offre();
        $form = $this->createForm('APM\AchatBundle\Form\Groupe_offreType', $groupe_offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($groupe_offre);
            $em->flush();

            return $this->redirectToRoute('apm_achat_groupe_show', array('id' => $groupe_offre->getId()));
        }

        return $this->render('APMAchatBundle:groupe_offre:new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Groupe_offre entity.
     *
     */
    public function showAction(Groupe_offre $groupe_offre)
    {
        $deleteForm = $this->createDeleteForm($groupe_offre);

        return $this->render('APMAchatBundle:groupe_offre:show.html.twig', array(
            'groupe_offre' => $groupe_offre,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Groupe_offre entity.
     *
     * @param Groupe_offre $groupe_offre The Groupe_offre entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Groupe_offre $groupe_offre)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_achat_groupe_delete', array('id' => $groupe_offre->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Groupe_offre entity.
     *
     */
    public function editAction(Request $request, Groupe_offre $groupe_offre)
    {
        $deleteForm = $this->createDeleteForm($groupe_offre);
        $editForm = $this->createForm('APM\AchatBundle\Form\Groupe_offreType', $groupe_offre);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($groupe_offre);
            $em->flush();

            return $this->redirectToRoute('apm_achat_groupe_show', array('id' => $groupe_offre->getId()));
        }

        return $this->render('APMAchatBundle:groupe_offre:edit.html.twig', array(
            'groupe_offre' => $groupe_offre,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Groupe_offre entity.
     *
     */
    public function deleteAction(Request $request, Groupe_offre $groupe_offre)
    {
        $form = $this->createDeleteForm($groupe_offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($groupe_offre);
            $em->flush();
        }

        return $this->redirectToRoute('apm_achat_groupe_index');
    }

    public function deleteFromListAction(Groupe_offre $groupe_offre)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($groupe_offre);
        $em->flush();

        return $this->redirectToRoute('apm_achat_groupe_index');
    }
}
