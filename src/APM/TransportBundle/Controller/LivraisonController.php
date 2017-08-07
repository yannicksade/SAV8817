<?php

namespace APM\TransportBundle\Controller;

use APM\TransportBundle\Entity\Livraison;
use APM\TransportBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Boutique;
use APM\VenteBundle\Entity\Transaction;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


/**
 * Livraison controller.
 *
 */
class LivraisonController extends Controller
{
    /**
     * Liste les livraisons enregistrées par un utilisateur ou par une boutique
     * les livraisons se font uniquement sur les opérations effectuées...
     * @param Boutique $boutique
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Boutique $boutique = null)
    {
        $this->listeAndShowSecurity($boutique);
        if (null !== $boutique) {
            $livraisons = $boutique->getLivraisons();
            //$livraisons = $livraisons->filter();
        } else {
            /** @var Utilisateur_avm $user */
            $user = $this->getUser();
            $livraisons = $user->getLivraisons();
        }
        return $this->render('APMTransportBundle:livraison:index_old.html.twig', array(
            'livraisons' => $livraisons,
            'boutique' => $boutique,
        ));
    }

    /**
     * @param Boutique $boutique
     * @param Utilisateur_avm |null $beneficiaire
     */
    private function listeAndShowSecurity($boutique, $beneficiaire = null)
    {
        //-----------------------------------security-------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $user = $this->getUser();
        if ($boutique) {
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
            if ($user !== $gerant && $user !== $proprietaire) {
                throw $this->createAccessDeniedException();
            }
        } else {//donne la possibilité au bénéficiaire de voir les détails de la livraison
            if ($beneficiaire) {
                if ($user !== $beneficiaire) {
                    throw $this->createAccessDeniedException();
                }
            }
        }

        //------------------------------------------------------------------------------------------
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function search($name, $value)
    {
        $this->listeAndShowSecurity($boutique);
        $em = $this->getDoctrine()->getManager();
        $livraisons = $em->getRepository('APMTransportBundle:Livraison')->findBy([$name => $value], ['orderBy' => 'DESC']);

        return $this->render('APMTransportBundle:livraison:index_old.html.twig', array(
            'livraisons' => $livraisons,
            'boutique' => $boutique,
        ));
    }

    public function listTransactionsAction(Livraison $livraison)
    {
        $boutique = $livraison->getBoutique();
        $transactionsEffectues = $livraison->getOperations();
        $transactionsRecues = null;
        return $this->render('APMVenteBundle:transaction:index_old.html.twig', array(
            'transactionsEffectues' => $transactionsEffectues,
            'transactionsRecues' => $transactionsRecues,
            'boutique' => $boutique,
        ));
    }

    /**
     * @ParamConverter("transaction", options={"mapping":{"transaction_id":"id"}})
     * Creates a new Livraison entity.
     * @param Request $request
     * @param Boutique $boutique
     * @param Transaction $transaction
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request, Boutique $boutique = null, Transaction $transaction = null)
    {
        $this->createSecurity($boutique, [$transaction]);
        /** @var Livraison $livraison */
        $livraison = TradeFactory::getTradeProvider("livraison");
        $form = $this->createForm('APM\TransportBundle\Form\LivraisonType', $livraison);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$transaction) {
                $transactions = $form->get('operations')->getData();
                $this->createSecurity($boutique, $transactions);
                /** @var Transaction $transaction */
                foreach ($transactions as $transaction) {
                    $transaction->setLivraison($livraison);
                    $transaction->setShipped(true);
                }
            } else {
                $this->createSecurity($boutique, $transaction);
                $transaction->setLivraison($livraison);
                $transaction->setShipped(true);
            }
            $livraison->setUtilisateur($this->getUser());
            $livraison->setBoutique($boutique);
            $em = $this->getDoctrine()->getManager();
            $em->persist($livraison);
            $em->flush();

            return $this->redirectToRoute('apm_transport_livraison_show', array('id' => $livraison->getId()));
        }

        return $this->render('APMTransportBundle:livraison:new.html.twig', array(
            'livraison' => $livraison,
            'form' => $form->createView(),
        ));
    }

    /**
     * @param Boutique $boutique
     * Verifie si la boutique appartient à son proprietaire ou le gerant
     * @param Collection $transactions
     */
    private function createSecurity($boutique, $transactions = null)
    {
        //--------security: verifie si l'utilisateur courant est le gerant de la boutique qui cree la livraison---------
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $gerant = null;
        $proprietaire = null;
        $user = $this->getUser();
        // une boutique ou tout utilisateur AVM peut créer des livraisons
        // mais uniquement pour leurs propres opérations...
        // On ne peut créer un
        if ($boutique) {
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
            if ($user !== $gerant && $user !== $proprietaire) {
                throw $this->createAccessDeniedException();
            }
        }
        if ($transactions) {
            /** @var Transaction $operation */
            foreach ($transactions as $operation) {//Vérifier l'identité des ayant droits
                if ($operation)
                    if ($operation->isShipped() || $boutique !== $operation->getBoutique() || $user !== $operation->getAuteur()) {
                        throw $this->createAccessDeniedException();
                    }
            }
        }
        //--------------------------------------------------------------------------------------------------------------
    }

    /**
     * @ParamConverter("transaction", options={"mapping":{"transaction_id":"id"}})
     * voir un livraison
     * @param Livraison $livraison
     * @param Transaction $transaction
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Livraison $livraison, Transaction $transaction = null)
    {
        if ($transaction) {
            $this->listeAndShowSecurity(null, $transaction->getBeneficiaire());
        } else {
            $this->listeAndShowSecurity($livraison->getBoutique());
        }
        $deleteForm = $this->createDeleteForm($livraison);

        return $this->render('APMTransportBundle:livraison:show.html.twig', array(
            'livraison' => $livraison,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Livraison entity.
     *
     * @param Livraison $livraison The Livraison entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Livraison $livraison)
    {
        $this->editAndDeleteSecurity($livraison);
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_transport_livraison_delete', array('id' => $livraison->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @param Livraison $livraison
     * @param Collection $transactions
     */
    private function editAndDeleteSecurity($livraison, $transactions = null)
    {//----- security : au cas ou il s'agirait d'une boutique vérifier le droit de l'utilisateur --------------------
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        $boutique = $livraison->getBoutique();
        $auteur = $livraison->getUtilisateur();
        $user = $this->getUser();
        $gerant = null;
        $proprietaire = null;

        if ($boutique) {
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
        }

        if ($transactions) {// idem que pour la créeation, vérifie l'identité de la boutique et des ayants droits
            /** @var Transaction $operation */
            foreach ($transactions as $operation)
                if (($user !== $operation->getAuteur() && $user !== $auteur) || $user !== $gerant && $user !== $proprietaire) {
                    throw $this->createAccessDeniedException();
                }
        }
        //--------------------------------------------------------------------------------------------------------------

    }

    /**
     * Displays a form to edit an existing Livraison entity.
     * @param Request $request
     * @param Livraison $livraison
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Livraison $livraison)
    {

        $this->editAndDeleteSecurity($livraison, $livraison->getOperations());

        $deleteForm = $this->createDeleteForm($livraison);
        $editForm = $this->createForm('APM\TransportBundle\Form\LivraisonType', $livraison);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $transactions = $editForm->get('operations')->getData();
            $this->editAndDeleteSecurity($livraison, $transactions);
            /** @var Transaction $transaction */
            foreach ($transactions as $transaction) {
                $transaction->setLivraison($livraison);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($livraison);
            $em->flush();

            return $this->redirectToRoute('apm_transport_livraison_show', array('id' => $livraison->getId()));
        }

        return $this->render('APMTransportBundle:livraison:edit.html.twig', array(
            'livraison' => $livraison,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Livraison entity.
     * @param Request $request
     * @param Livraison $livraison
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Livraison $livraison)
    {
        $this->editAndDeleteSecurity($livraison, $livraison->getOperations());

        $form = $this->createDeleteForm($livraison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->editAndDeleteSecurity($livraison);
            $em = $this->getDoctrine()->getManager();
            $em->remove($livraison);
            $em->flush();
        }

        return $this->redirectToRoute('apm_transport_livraison_index');
    }

    public function deleteFromListAction(Livraison $livraison)
    {
        $this->editAndDeleteSecurity($livraison, $livraison->getOperations());
        $em = $this->getDoctrine()->getManager();
        $em->remove($livraison);
        $em->flush();

        return $this->redirectToRoute('apm_transport_livraison_index');
    }
}
