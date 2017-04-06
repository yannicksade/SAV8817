<?php

namespace APM\AchatBundle\Controller;

use APM\AchatBundle\Entity\Service_apres_vente;
use APM\AchatBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Offre;
use APM\VenteBundle\Entity\Transaction_produit;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Service_apres_vente controller.
 *
 */
class Service_apres_venteController extends Controller
{
    /**
     * liste les SAV du clients
     * @return \Symfony\Component\HttpFoundation\Response Liste tous les SAV d'un client
     *
     * Liste tous les SAV d'un client
     */
    public function indexAction()
    {
        $this->listAndShowSecurity();
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $service_apres_ventes = $user->getServicesApresVentes();
        return $this->render('APMAchatBundle:service_apres_vente:index.html.twig', array(
            'service_apres_ventes' => $service_apres_ventes,
        ));
    }

    /**
     * un callback de securite prevoit que le client à effectivement achete l'offre
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $this->createSecurity();

        /** @var Service_apres_vente $service_apres_vente */
        $service_apres_vente = TradeFactory::getTradeProvider("service_apres_vente");
        $form = $this->createForm('APM\AchatBundle\Form\Service_apres_venteType', $service_apres_vente);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->createSecurity($form->getData()['offre']);
            $service_apres_vente->setClient($this->getUser());
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
     * @param Service_apres_vente $service_apres_vente
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * Afficher ls détails d'une SAV
     */
    public function showAction(Service_apres_vente $service_apres_vente)
    {
        $this->listAndShowSecurity();

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
     * @param Request $request
     * @param Service_apres_vente $service_apres_vente
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Service_apres_vente $service_apres_vente)
    {
        $this->editAndDeleteSecurity($service_apres_vente);
        $deleteForm = $this->createDeleteForm($service_apres_vente);
        $editForm = $this->createForm('APM\AchatBundle\Form\Service_apres_venteType', $service_apres_vente);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->editAndDeleteSecurity($service_apres_vente);
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
     * @param Request $request
     * @param Service_apres_vente $service_apres_vente
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Service_apres_vente $service_apres_vente)
    {
        $this->editAndDeleteSecurity($service_apres_vente);

        $form = $this->createDeleteForm($service_apres_vente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->editAndDeleteSecurity($service_apres_vente);
            $em = $this->getDoctrine()->getManager();
            $em->remove($service_apres_vente);
            $em->flush();
        }

        return $this->redirectToRoute('apm_achat_service_apres_vente_index');
    }

    public function deleteFromListAction(Service_apres_vente $service_apres_vente)
    {
        $this->editAndDeleteSecurity($service_apres_vente);

        $em = $this->getDoctrine()->getManager();
        $em->remove($object);
        $em->flush();

        return $this->redirectToRoute('apm_achat_service_apres_vente_index');
    }

    private function listAndShowSecurity(){
        //-----------------------------------security-------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw $this->createAccessDeniedException();
        }
        //-----------------------------------------------------------------------------------------
    }


    /**
     * @param Offre|null $offre
     */
    private function createSecurity($offre = null){
        //-----------------security: L'utilisateur doit etre le client qui a acheté l'offre -----------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $clientDeOffre = false;
        if($offre){
            $transactions = $offre->getProduitTransactions();
            /** @var Transaction_produit $transaction */
            foreach ($transactions as $transaction){
                $produit = $transaction->getProduit();
                $auteur = $transaction->getProduit();
                if($produit === $offre && $auteur === $this->getUser()) {
                    $clientDeOffre = true;
                }
            }
            if(!$clientDeOffre)  throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * @param Service_apres_vente $service_apres_vente
     */
    private function editAndDeleteSecurity($service_apres_vente){
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');

        /* ensure that the user is logged in  # granted even through remembering cookies
        *  and that the one is the owner
        */
        $client = $service_apres_vente->getClient();
        $user = $this->getUser();
        if ((!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) || ($client!== $user)) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }
}
