<?php

namespace APM\MarketingDistribueBundle\Controller;

use APM\MarketingDistribueBundle\Entity\Commissionnement;
use APM\MarketingDistribueBundle\Entity\Conseiller_boutique;
use APM\MarketingDistribueBundle\Entity\Quota;
use APM\MarketingDistribueBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Admin;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Boutique;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Commissionnement controller.
 *
 */
class CommissionnementController extends Controller
{
    /**
     * Liste les commissionnements d'un conseiller ou d'une boutique pour jouir il faut avoir definir son profile conseiller
     * @param Boutique $boutique
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Boutique $boutique = null)
    {
        $boutiques_commissionnements = null;
        if ($boutique) {
            $this->listAndShowSecurity(null, $boutique);
            $boutiqueConseillers = $boutique->getBoutiqueConseillers();
            if ($boutiqueConseillers) {
                /** @var Conseiller_boutique $boutiqueConseiller */
                foreach ($boutiqueConseillers as $boutiqueConseiller) {
                    $boutiques_commissionnements [] = array(
                        'boutique' => $boutiqueConseiller->getBoutique(),
                        'commissionnements' => $boutiqueConseiller->getCommissionnements(),
                        'conseiller' => $boutiqueConseiller->getConseiller(),
                    );
                }
            }

        } else {
            $this->listAndShowSecurity();
            /** @var Utilisateur_avm $user */
            $user = $this->getUser();
            $conseiller = $user->getProfileConseiller();
            if ($conseiller) {
                $conseiller_boutiques = $conseiller->getConseillerBoutiques();
                if ($conseiller_boutiques) {
                    /** @var Conseiller_boutique $conseiller_boutique */
                    foreach ($conseiller_boutiques as $conseiller_boutique) {
                        $boutiques_commissionnements [] = array(
                            'boutique' => $conseiller_boutique->getBoutique(),
                            'commissionnements' => $conseiller_boutique->getCommissionnements(),
                            'conseiller' => $conseiller_boutique->getConseiller(),
                        );
                    }
                }
            }
        }

        return $this->render('APMMarketingDistribueBundle:commissionnement:index.html.twig', array(
            'boutiques_commissionnements' => $boutiques_commissionnements,
        ));
    }

    /**
     * @param Commissionnement |null $commissionnement
     * @param Boutique $boutique
     */
    private function listAndShowSecurity($commissionnement = null, $boutique = null)
    {
        //---------------------------------security-----------------------------------------------
        $this->denyAccessUnlessGranted(['ROLE_CONSEILLER', 'ROLE_BOUTIQUE'], null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $gerant = null;
        $proprietaire = null;
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $conseiller = $user->getProfileConseiller();
        if ($conseiller) $conseiller = $conseiller->getUtilisateur();
        if ($commissionnement) {
            $boutique = $commissionnement->getCommission()->getBoutiqueProprietaire();
            $conseiller = $commissionnement->getConseillerBoutique()->getConseiller()->getUtilisateur();
            $gerant = null;
            $proprietaire = null;
        }
        if ($boutique) {
            $gerant = $boutique->getGerant();
            $proprietaire = $boutique->getProprietaire();
            $conseiller = null;
        }
        if ($user !== $conseiller && $user !== $gerant && $user !== $proprietaire) {
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * Une boutique créé des commissionnements pour des conseiller de la boutique
     * @param Request $request
     * @param Boutique $boutique
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request, Boutique $boutique)
    {
        $this->createSecurity($boutique);
        /** @var Commissionnement $commissionnement */
        $commissionnement = TradeFactory::getTradeProvider("commissionnement");
        $form = $this->createForm('APM\MarketingDistribueBundle\Form\CommissionnementType', $commissionnement);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $quota = $form->get('commission')->getData();
            $this->createSecurity($boutique, $quota);
            $em = $this->getDoctrine()->getManager();
            $em->persist($commissionnement);
            $em->flush();

            return $this->redirectToRoute('apm_marketing_commissionnement_show', array('id' => $commissionnement->getId()));
        }

        return $this->render('APMMarketingDistribueBundle:commissionnement:new.html.twig', array(
            'commissionnement' => $commissionnement,
            'form' => $form->createView(),
            'boutique' => $boutique,
        ));
    }

    /**
     * @param Boutique $boutique
     * @param Quota $quota
     */
    private function createSecurity($boutique, $quota = null)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have the required role
        $this->denyAccessUnlessGranted('ROLE_BOUTIQUE', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        /* ensure that the user is logged in
        *  and that the one is the owner
        */
        //la boutique pour laquelle le conseiller beneficie les commissionnements doit être la même qui offre le Quota
        $user = $this->getUser();
        $proprietaire = $boutique->getProprietaire();
        $gerant = $boutique->getGerant();
        if (null !== $quota && $quota->getBoutiqueProprietaire() !== $boutique || $user !== $gerant && $user !== $proprietaire)
            throw $this->createAccessDeniedException();
        //----------------------------------------------------------------------------------------
    }

    /**
     * Finds and displays a Commissionnement entity.
     * @param Commissionnement $commissionnement
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Commissionnement $commissionnement)
    {

        $this->listAndShowSecurity($commissionnement);

        $deleteForm = $this->createDeleteForm($commissionnement);

        return $this->render('APMMarketingDistribueBundle:commissionnement:show.html.twig', array(
            'commissionnement' => $commissionnement,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Commissionnement entity.
     *
     * @param Commissionnement $commissionnement The Commissionnement entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Commissionnement $commissionnement)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_marketing_commissionnement_delete', array('id' => $commissionnement->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Commissionnement entity.
     * @param Request $request
     * @param Commissionnement $commissionnement
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Commissionnement $commissionnement)
    {
        $this->editSecurity();
        $deleteForm = $this->createDeleteForm($commissionnement);
        $editForm = $this->createForm('APM\MarketingDistribueBundle\Form\CommissionnementType', $commissionnement);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->editSecurity();
            $em = $this->getDoctrine()->getManager();
            $em->persist($commissionnement);
            $em->flush();

            return $this->redirectToRoute('apm_marketing_commissionnement_show', array('id' => $commissionnement->getId()));
        }

        return $this->render('APMMarketingDistribueBundle:commissionnement:edit.html.twig', array(
            'commissionnement' => $commissionnement,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    private function editSecurity()
    {
        //---------------------------------security-----------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN', null, 'Unable to access this page!');
        $user = $this->getUser();
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')
            || !($user instanceof Admin)
        ) throw $this->createAccessDeniedException();
    }

    /**
     * Supprimer à partir d'un formulaire
     * @param Request $request
     * @param Commissionnement $commissionnement
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Commissionnement $commissionnement)
    {
        $this->deleteSecurity($commissionnement);
        $form = $this->createDeleteForm($commissionnement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->deleteSecurity($commissionnement);
            $em = $this->getDoctrine()->getManager();
            $em->remove($commissionnement);
            $em->flush();
        }

        return $this->redirectToRoute('apm_marketing_commissionnement_index');
    }
    //-------------------------------------------------------

    /**
     * Le conseiller peut supprimer ses commissionnement
     * @param Commissionnement $commissionnement
     */
    private function deleteSecurity($commissionnement)
    {
        //---------------------------------security-----------------------------------------------
        $this->denyAccessUnlessGranted('ROLE_CONSEILLER', null, 'Unable to access this page!');
        $user = $this->getUser();
        if ((!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
            || ($commissionnement->getConseillerBoutique()->getConseiller()->getUtilisateur() !== $user)
        ) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------
    }

    public function deleteFromListAction(Commissionnement $commissionnement)
    {
        $this->deleteSecurity($commissionnement);
        $em = $this->getDoctrine()->getManager();
        $em->remove($commissionnement);
        $em->flush();

        return $this->redirectToRoute('apm_marketing_commissionnement_index');
    }
    //----------------------------------------------------------------------------------------
}

