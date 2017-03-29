<?php
namespace APM\VenteBundle\Controller;

use APM\VenteBundle\Entity\Boutique;
use APM\VenteBundle\TradeAbstraction\Trade;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Boutique controller.
 *
 */
class BoutiqueController extends Controller
{

    /**
     * Lists all Boutique entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $boutiques = $em->getRepository('APMVenteBundle:Boutique')->findAll();

        return $this->render('APMVenteBundle:boutique:index.html.twig', array(
            'boutiques' => $boutiques,
        ));
    }

    /**
     * Creates a new Boutique entity.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        /** @var Boutique $boutique */
        $boutique = Trade::getTradeProvider('boutique');
        $form = $this->createForm('APM\VenteBundle\Form\BoutiqueType', $boutique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getEM();
            $em->persist($boutique);
            $em->flush();

            return $this->redirectToRoute('apm_vente_boutique_show', array('id' => $boutique->getId()));
        }
        return $this->render('APMVenteBundle:boutique:new.html.twig', array(
            'form' => $form->createView(),
        ));

    }

    private function getEM()
    {
        return $this->get('doctrine.orm.entity_manager');
    }

    /**
     * Finds and displays a Boutique entity.
     * @param Boutique $boutique
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Boutique $boutique)
    {
        $deleteForm = $this->createDeleteForm($boutique);

        return $this->render('APMVenteBundle:boutique:show.html.twig', array(
            'boutique' => $boutique,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Boutique entity.
     *
     * @param Boutique $boutique The Boutique entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Boutique $boutique)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_vente_boutique_delete', array('id' => $boutique->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Boutique entity.
     * @param Request $request
     * @param Boutique $boutique
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Boutique $boutique)
    {
        $deleteForm = $this->createDeleteForm($boutique);
        $editForm = $this->createForm('APM\VenteBundle\Form\BoutiqueType', $boutique);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getEM();
            $em->persist($boutique);
            $em->flush();

            return $this->redirectToRoute('apm_vente_boutique_show', array('id' => $boutique->getId()));
        }

        return $this->render('APMVenteBundle:boutique:edit.html.twig', array(
            'boutique' => $boutique,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Boutique entity.
     * @param Request $request
     * @param Boutique $boutique
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Boutique $boutique)
    {
        $form = $this->createDeleteForm($boutique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getEM();
            $em->remove($boutique);
            $em->flush();
        }

        return $this->redirectToRoute('apm_vente_boutique_index');
    }

    public function deleteFromListAction(Boutique $object)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($object);
        $em->flush();

        return $this->redirectToRoute('apm_vente_boutique_index');
    }
}
