<?php

namespace APM\VenteBundle\Controller;

use APM\VenteBundle\Entity\Categorie;
use APM\VenteBundle\Form\CategorieType;
use APM\VenteBundle\TradeAbstraction\Trade;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Categorie controller.
 *
 */
class CategorieController extends Controller
{

    /**
     * Lists all Categorie entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getEM();
        $categories = $em->getRepository('APMVenteBundle:Categorie')->findAll();

        return $this->render('APMVenteBundle:categorie:index.html.twig', array(
            'categories' => $categories,
        ));
    }

    private function getEM()
    {
        return $this->get('doctrine.orm.entity_manager');
    }

    /**
     * Creates a new Categorie entity.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        /** @var Categorie $categorie */
        $categorie = Trade::getTradeProvider('categorie');
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getEM();
            $em->persist($categorie);
            $em->flush();

            return $this->redirectToRoute('apm_vente_categorie_show', array('id' => $categorie->getId()));
        }
        return $this->render('APMVenteBundle:categorie:new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Categorie entity.
     * @param Categorie $categorie
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Categorie $categorie)
    {
        $deleteForm = $this->createDeleteForm($categorie);

        return $this->render('APMVenteBundle:categorie:show.html.twig', array(
            'categorie' => $categorie,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Categorie entity.
     *
     * @param Categorie $categorie The Categorie entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Categorie $categorie)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_vente_categorie_delete', array('id' => $categorie->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Categorie entity.
     * @param Request $request
     * @param Categorie $categorie
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Categorie $categorie)
    {
        $deleteForm = $this->createDeleteForm($categorie);
        $editForm = $this->createForm(CategorieType::class, $categorie);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getEM();
            $em->persist($categorie);
            $em->flush();

            return $this->redirectToRoute('apm_vente_categorie_show', array('id' => $categorie->getId()));
        }

        return $this->render('APMVenteBundle:categorie:edit.html.twig', array(
            'categorie' => $categorie,
            'delete_form' => $deleteForm->createView(),
            'edit_form' => $editForm->createView()

        ));
    }

    /**
     * Deletes a Categorie entity.
     * @param Request $request
     * @param Categorie $categorie
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Categorie $categorie)
    {
        $form = $this->createDeleteForm($categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getEM();
            $em->remove($categorie);
            $em->flush();
        }

        return $this->redirectToRoute('apm_vente_categorie_index');
    }

    public function deleteFromListAction(Categorie $object)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($object);
        $em->flush();

        return $this->redirectToRoute('apm_vente_categorie_index');
    }
}
