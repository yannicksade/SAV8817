<?php

namespace APM\VenteBundle\Controller;

use APM\VenteBundle\Entity\Boutique;
use APM\VenteBundle\Entity\Categorie;
use APM\VenteBundle\Factory\TradeFactory;
use APM\VenteBundle\Form\CategorieType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Categorie controller.
 *
 */
class CategorieController extends Controller
{

    /**
     * Liste les catégories par Boutique
     * @param Boutique $boutique
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Boutique $boutique)
    {
        $this->listAndShowSecurity($boutique);
        $categories = $boutique->getCategories();

        return $this->render('APMVenteBundle:categorie:index_old.html.twig', array(
            'categories' => $categories,
            'boutique' => $boutique
        ));
    }

    /**
     * @param Boutique $boutique
     */
    private function listAndShowSecurity($boutique = null)
    {
        //-----------------------------------security-------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted(['ROLE_BOUTIQUE', 'ROLE_USERAVM'], null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw $this->createAccessDeniedException();
        }
        if ($boutique) {
            $user = $this->getUser();
            $proprietaire = $boutique->getProprietaire();
            $gerant = $boutique->getGerant();
            if ($user !== $gerant && $user !== $proprietaire) {
                throw $this->createAccessDeniedException();
            }
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * Creates les catégories sont créées uniquement dans les boutiques
     * @param Request $request
     * @param Boutique $boutique
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request, Boutique $boutique = null)
    {
        $this->createSecurity($boutique);
        /** @var Categorie $categorie */
        $categorie = TradeFactory::getTradeProvider('categorie');
        $categorie->setBoutique($boutique);
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->createSecurity($boutique, $form->get('categorieCourante')->getData());
           $em= $this->getEM();
            $em->persist($categorie);
            $em->flush();

            return $this->redirectToRoute('apm_vente_categorie_show', array('id' => $categorie->getId()));
        }
        return $this->render('APMVenteBundle:categorie:new.html.twig', array(
            'form' => $form->createView(),
            'boutique' => $boutique
        ));
    }

    /**
     * @param Boutique $boutique
     * @param Categorie $categorieCourante
     */
    private function createSecurity($boutique = null, $categorieCourante = null)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_BOUTIQUE', null, 'Unable to access this page!');

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        //Interdire tout utilisateur si ce n'est pas le gerant ou le proprietaire
        if ($boutique) {
            $user = $this->getUser();
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
            if ($user !== $gerant && $user !== $proprietaire) {
                throw $this->createAccessDeniedException();
            }
            if ($categorieCourante) {
                $currentBoutique = $categorieCourante->getBoutique();
                if ($currentBoutique !== $boutique) {
                    throw $this->createAccessDeniedException();
                }
            }
        }
        //----------------------------------------------------------------------------------------
    }

    private function getEM()
    {
        return $this->get('doctrine.orm.entity_manager');
    }

    /**
     * Finds and displays a Categorie entity.
     * @param Categorie $categorie
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Categorie $categorie)
    {
        $this->listAndShowSecurity();
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
        $this->editAndDeleteSecurity($categorie);

        $deleteForm = $this->createDeleteForm($categorie);
        $editForm = $this->createForm(CategorieType::class, $categorie);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->editAndDeleteSecurity($categorie);
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
     * @param Categorie $categorie
     */
    private function editAndDeleteSecurity($categorie)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_BOUTIQUE', null, 'Unable to access this page!');

        /* ensure that the user is logged in
        *  and that the one is the owner
         * Interdire tout utilisateur si ce n'est pas le gerant ou le proprietaire
        */
        $user = $this->getUser();
        $boutique = $categorie->getBoutique();
        $gerant = $boutique->getGerant();
        $proprietaire = $boutique->getProprietaire();
        if ((!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) || ($gerant !== $user && $user !== $proprietaire)) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------

    }

    /**
     * Deletes a Categorie entity.
     * @param Request $request
     * @param Categorie $categorie
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Categorie $categorie)
    {
        $this->editAndDeleteSecurity($categorie);

        $form = $this->createDeleteForm($categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->editAndDeleteSecurity($categorie);
            $em = $this->getEM();
            $em->remove($categorie);
            $em->flush();
        }

        return $this->redirectToRoute('apm_vente_categorie_index', ['id' =>$categorie->getBoutique()->getId()]);
    }

    public function deleteFromListAction(Categorie $categorie)
    {
        $this->editAndDeleteSecurity($categorie);

        $em = $this->getDoctrine()->getManager();
        $em->remove($object);
        $em->flush();

        return $this->redirectToRoute('apm_vente_categorie_index', ['id' =>$categorie->getBoutique()->getId()]);
    }
}
