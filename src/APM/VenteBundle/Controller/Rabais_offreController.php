<?php

namespace APM\VenteBundle\Controller;

use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Offre;
use APM\VenteBundle\Entity\Rabais_offre;
use APM\VenteBundle\Factory\TradeFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Rabais_offre controller.
 *
 */
class Rabais_offreController extends Controller
{

    /**
     * Le vendeur crée des rabais pour un utilisateur donné
     * Liste les rabais créé par le vendeur
     * @param Offre $offre
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Offre $offre = null)
    {
        $this->listAndShowSecurity($offre);
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $rabais_offres = null;
        if ($offre) $rabais_offres = $offre->getRabais();
        $rabais_recus = $user->getRabaisRecus();
        $rabais_accordes = $user->getRabaisAccordes();
        return $this->render('APMVenteBundle:rabais_offre:index.html.twig', array(
            'rabais_offres' => $rabais_offres,
            'rabais_recus' => $rabais_recus,
            'rabais_accordes' => $rabais_accordes,
            'offre' => $offre,
        ));
    }

    /**
     * @param Rabais_offre|null $rabais
     * @param Offre $offre
     */
    private function listAndShowSecurity($offre = null, $rabais = null)
    {
        //-----------------------------------security-------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw $this->createAccessDeniedException();
        }

        if ($offre) { /// N'autorise que le vendeur, le gerant ou le proprietaire ou le bénéficiare à pouvoir afficher des rabais sur l'offre
            $user = $this->getUser();
            $boutique = $offre->getBoutique();
            $gerant = null;
            $proprietaire = null;
            $beneficiaire = null;
            if ($boutique) {
                $gerant = $boutique->getGerant();
                $proprietaire = $boutique->getProprietaire();
            }
            $vendeur = $offre->getVendeur();
            if ($rabais) {//beneficiare
                $beneficiaire = $rabais->getBeneficiaireRabais();
            }
            if ($user !== $gerant && $user !== $proprietaire && $user !== $vendeur && $user !== $beneficiaire) {
                throw $this->createAccessDeniedException();
            }
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * Creates a new Rabais_offre entity.
     * @param Request $request
     * @param Offre $offre
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request, Offre $offre)
    {
        $this->createSecurity($offre);
        /** @var Rabais_offre $rabais */
        $rabais = TradeFactory::getTradeProvider('rabais');
        $rabais->setOffre($offre);
        $form = $this->createForm('APM\VenteBundle\Form\Rabais_offreType', $rabais);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->createSecurity($offre, $rabais);
            $rabais->setVendeur($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($rabais);
            $em->flush();

            return $this->redirectToRoute('apm_vente_rabais_offre_show', array('id' => $rabais->getId()));
        }

        return $this->render('APMVenteBundle:rabais_offre:new.html.twig', array(
            'offre' => $offre,
            'form' => $form->createView(),
        ));
    }

    /**
     * @param Offre $offre
     * @param Rabais_offre|null $rabais
     */
    private function createSecurity($offre = null, $rabais = null)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        if ($offre) { /// N'autorise que le vendeur, le gerant ou le proprietaire à pouvoir  faire des rabais
            $user = $this->getUser();
            $boutique = $offre->getBoutique();
            $gerant = null;
            $proprietaire = null;
            $beneficiaire = null;
            if ($boutique) {
                $gerant = $boutique->getGerant();
                $proprietaire = $boutique->getProprietaire();
            }
            $vendeur = $offre->getVendeur();
            if ($rabais) $beneficiaire = $rabais->getBeneficiaireRabais();
            //le beneficiaire du rabais ne peut être celui qui le cree et le createur ne devrait être que le vendeur ayant droit,
            if ($user !== $gerant && $user !== $proprietaire && $user !== $vendeur || $beneficiaire === $user) {
                throw $this->createAccessDeniedException();
            }
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * Finds and displays a Rabais_offre entity.
     * @param Rabais_offre $rabais_offre
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Rabais_offre $rabais_offre)
    {
        $this->listAndShowSecurity($rabais_offre->getOffre(), $rabais_offre);
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
        $this->editAndDeleteSecurity($rabais_offre);
        $deleteForm = $this->createDeleteForm($rabais_offre);
        $editForm = $this->createForm('APM\VenteBundle\Form\Rabais_offreType', $rabais_offre);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->editAndDeleteSecurity($rabais_offre);
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
     * @param Rabais_offre $rabais
     */
    private function editAndDeleteSecurity($rabais)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a USERAVM role
        $this->denyAccessUnlessGranted('ROLE_USERAVM', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        /// N'autorise que le vendeur, le gerant ou le proprietaire à pouvoir modifier ou supprimer des rabais sur l'offre
        // à condition qu'ils ne soyent pas ledit bénéficiaire
        $boutique = $rabais->getOffre()->getBoutique();
        $user = $this->getUser();
        $gerant = null;
        $proprietaire = null;
        if ($boutique) {
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
        }
        $vendeur = $rabais->getOffre()->getVendeur();
        if ($user !== $gerant && $user !== $proprietaire && $user !== $vendeur || $user === $rabais->getBeneficiaireRabais()) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------

    }

    /**
     * Deletes a Rabais_offre entity.
     * @param Request $request
     * @param Rabais_offre $rabais_offre
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Rabais_offre $rabais_offre)
    {
        $this->editAndDeleteSecurity($rabais_offre);
        $form = $this->createDeleteForm($rabais_offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->editAndDeleteSecurity($rabais_offre);
            $em = $this->getDoctrine()->getManager();
            $em->remove($rabais_offre);
            $em->flush();
        }

        return $this->redirectToRoute('apm_vente_rabais_offre_index', ['id' => $rabais_offre->getOffre()->getId()]);
    }

    public function deleteFromListAction(Rabais_offre $rabais_offre)
    {
        $this->editAndDeleteSecurity($rabais_offre);

        $em = $this->getDoctrine()->getManager();
        $em->remove($rabais_offre);
        $em->flush();

        return $this->redirectToRoute('apm_vente_rabais_offre_index', ['id' => $rabais_offre->getOffre()->getId()]);
    }
}
