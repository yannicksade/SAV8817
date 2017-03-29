<?php

namespace APM\AchatBundle\Controller;

use APM\AchatBundle\Entity\Service_apres_vente;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Service_apres_vente controller.
 *
 */
class Service_apres_venteController extends Controller
{
    /**
     * Lists all Service_apres_vente entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $service_apres_ventes = $em->getRepository('APMAchatBundle:Service_apres_vente')->findAll();

        return $this->render('APMAchatBundle:service_apres_vente:index.html.twig', array(
            'service_apres_ventes' => $service_apres_ventes,
        ));
    }

    /**
     * Creates a new Service_apres_vente entity.
     *
     */
    public function newAction(Request $request)
    {
        $service_apres_vente = new Service_apres_vente();
        $form = $this->createForm('APM\AchatBundle\Form\Service_apres_venteType', $service_apres_vente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($service_apres_vente);
            $em->flush();

            return $this->redirectToRoute('apm_achat_service_apres_vente_show', array('id' => $service_apres_vente->getId()));
        }

        return $this->render('APMAchatBundle:service_apres_vente:new.html.twig', array(
            'service_apres_vente' => $service_apres_vente,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Service_apres_vente entity.
     *
     */
    public function showAction(Service_apres_vente $service_apres_vente)
    {
        $deleteForm = $this->createDeleteForm($service_apres_vente);

        return $this->render('APMAchatBundle:service_apres_vente:show.html.twig', array(
            'service_apres_vente' => $service_apres_vente,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Service_apres_vente entity.
     *
     * @param Service_apres_vente $service_apres_vente The Service_apres_vente entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Service_apres_vente $service_apres_vente)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_achat_service_apres_vente_delete', array('id' => $service_apres_vente->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Service_apres_vente entity.
     *
     */
    public function editAction(Request $request, Service_apres_vente $service_apres_vente)
    {
        $deleteForm = $this->createDeleteForm($service_apres_vente);
        $editForm = $this->createForm('APM\AchatBundle\Form\Service_apres_venteType', $service_apres_vente);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($service_apres_vente);
            $em->flush();

            return $this->redirectToRoute('apm_achat_service_apres_vente_show', array('id' => $service_apres_vente->getId()));
        }

        return $this->render('APMAchatBundle:service_apres_vente:edit.html.twig', array(
            'service_apres_vente' => $service_apres_vente,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Service_apres_vente entity.
     *
     */
    public function deleteAction(Request $request, Service_apres_vente $service_apres_vente)
    {
        $form = $this->createDeleteForm($service_apres_vente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($service_apres_vente);
            $em->flush();
        }

        return $this->redirectToRoute('apm_achat_service_apres_vente_index');
    }

    public function deleteFromListAction(Service_apres_vente $object)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($object);
        $em->flush();

        return $this->redirectToRoute('apm_achat_service_apres_vente_index');
    }
}
