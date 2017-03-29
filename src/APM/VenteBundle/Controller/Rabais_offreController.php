<?php

namespace APM\VenteBundle\Controller;

use APM\VenteBundle\Entity\Rabais_offre;
use APM\VenteBundle\TradeAbstraction\Trade;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Rabais_offre controller.
 *
 */
class Rabais_offreController extends Controller
{

    /**
     * Lists all Rabais_offre entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $rabais_offres = $em->getRepository('APMVenteBundle:Rabais_offre')->findAll();

        return $this->render('APMVenteBundle:rabais_offre:index.html.twig', array(
            'rabais_offres' => $rabais_offres,
        ));
    }

    /**
     * Creates a new Rabais_offre entity.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        /** @var Rabais_offre $rabais */
        $rabais = Trade::getTradeProvider('rabais');
        $form = $this->createForm('APM\VenteBundle\Form\Rabais_offreType', $rabais);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($rabais);
            $em->flush();

            return $this->redirectToRoute('apm_vente_rabais_offre_show', array('id' => $rabais->getId()));
        }

        return $this->render('APMVenteBundle:rabais_offre:new.html.twig', array(
            'rabais_offre' => $rabais,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Rabais_offre entity.
     * @param Rabais_offre $rabais_offre
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Rabais_offre $rabais_offre)
    {
        $deleteForm = $this->createDeleteForm($rabais_offre);

        return $this->render('APMVenteBundle:rabais_offre:show.html.twig', array(
            'rabais_offre' => $rabais_offre,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Rabais_offre entity.
     *
     * @param Rabais_offre $rabais_offre The Rabais_offre entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Rabais_offre $rabais_offre)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_vente_rabais_offre_delete', array('id' => $rabais_offre->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Rabais_offre entity.
     * @param Request $request
     * @param Rabais_offre $rabais_offre
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Rabais_offre $rabais_offre)
    {
        $deleteForm = $this->createDeleteForm($rabais_offre);
        $editForm = $this->createForm('APM\VenteBundle\Form\Rabais_offreType', $rabais_offre);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($rabais_offre);
            $em->flush();

            return $this->redirectToRoute('apm_vente_rabais_offre_show', array('id' => $rabais_offre->getId()));
        }

        return $this->render('APMVenteBundle:rabais_offre:edit.html.twig', array(
            'rabais_offre' => $rabais_offre,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Rabais_offre entity.
     * @param Request $request
     * @param Rabais_offre $rabais_offre
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Rabais_offre $rabais_offre)
    {
        $form = $this->createDeleteForm($rabais_offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($rabais_offre);
            $em->flush();
        }

        return $this->redirectToRoute('apm_vente_rabais_offre_index');
    }

    public function deleteFromListAction(Rabais_offre $object)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($object);
        $em->flush();

        return $this->redirectToRoute('apm_vente_rabais_offre_index');
    }
}
