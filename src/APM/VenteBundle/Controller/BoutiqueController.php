<?php
namespace APM\VenteBundle\Controller;

use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Boutique;
use APM\VenteBundle\Factory\TradeFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Boutique controller.
 *
 */
class BoutiqueController extends Controller
{

    /**
     * Liste les boutiques de l'utilisateur
     *
     */
    public function indexAction()
    {
        $this->listAndShowSecurity();
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $boutiques = $user->getBoutiquesProprietaire();
        $boutiquesGerant = $user->getBoutiquesGerant();
        return $this->render('APMVenteBundle:boutique:index.html.twig', array(
            'boutiquesProprietaire' => $boutiques,
            'boutiquesGerant' => $boutiquesGerant,
        ));
    }

    /**
     * Creates a new Boutique entity.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $this->createSecurity();
        /** @var Boutique $boutique */
        $boutique = TradeFactory::getTradeProvider('boutique');
        $form = $this->createForm('APM\VenteBundle\Form\BoutiqueType', $boutique);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->createSecurity();
            $boutique->setProprietaire($this->getUser());
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
        $this->listAndShowSecurity();
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
        $this->editAndDeleteSecurity($boutique);
        $deleteForm = $this->createDeleteForm($boutique);
        $editForm = $this->createForm('APM\VenteBundle\Form\BoutiqueType', $boutique);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->editAndDeleteSecurity($boutique);
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
        $this->editAndDeleteSecurity($boutique);
        $form = $this->createDeleteForm($boutique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->editAndDeleteSecurity($boutique);
            $em = $this->getEM();
            $em->remove($boutique);
            $em->flush();
        }

        return $this->redirectToRoute('apm_vente_boutique_index');
    }

    public function deleteFromListAction(Boutique $boutique)
    {
        $this->editAndDeleteSecurity($boutique);
        $em = $this->getDoctrine()->getManager();
        $em->remove($boutique);
        $em->flush();

        return $this->redirectToRoute('apm_vente_boutique_index');
    }


    private function listAndShowSecurity(){
        //-----------------------------------security-------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_BOUTIQUE', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED'))  {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    private function createSecurity(){
        //---------------------------------security-----------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_BOUTIQUE', null, 'Unable to access this page!');
        /* ensure that the user is logged in
        */
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @param Boutique $boutique
     */
    private function editAndDeleteSecurity($boutique){
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_BOUTIQUE', null, 'Unable to access this page!');

        /* ensure that the user is logged in  # granted even through remembering cookies
        *  and that the one is the owner
        */
        $user = $this->getUser();
        if ((!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) || ($boutique->getProprietaire() !== $user)) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------

    }
}
