<?php

namespace APM\MarketingDistribueBundle\Controller;


use APM\MarketingDistribueBundle\Entity\Conseiller;
use APM\MarketingDistribueBundle\Entity\Conseiller_boutique;
use APM\MarketingDistribueBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Boutique;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Conseiller_boutique controller.
 *
 */
class Conseiller_boutiqueController extends Controller
{
    /**
     * Liste les boutiques d'un conseiller ou les conseillers liés à une boutique donnée
     * @param Boutique $boutique
     * @return \Symfony\Component\HttpFoundation\Response Lister les boutique du conseiller
     *
     * Lister les boutique du conseiller
     */
    public function indexAction(Boutique $boutique = null)
    {
        $this->listAndShowSecurity();
        if (null === $boutique) {
            /** @var Utilisateur_avm $user */
            $user = $this->getUser();
            $boutiques = null;
            $conseiller_boutiques = $user->getProfileConseiller()->getConseillerBoutiques();
            /** @var Conseiller_boutique $conseiller_boutique */
            foreach ($conseiller_boutiques as $conseiller_boutique) {
                $boutiques [] = $conseiller_boutique->getBoutique();
            }
            return $this->render('APMVenteBundle:boutique:index.html.twig', array(
                'boutiquesProprietaire' => $boutiques,
                'boutiquesGerant' => null,
            ));
        } else {
            $boutique_conseillers = $boutique->getBoutiqueConseillers();
            $conseillers = null;
            /** @var Conseiller_boutique $boutique_conseiller */
            foreach ($boutique_conseillers as $boutique_conseiller) {
                $conseillers [] = $boutique_conseiller->getConseiller();
            }
            return $this->render('APMMarketingDistribueBundle:conseiller:index_old.html.twig', array(
                'conseillers' => $conseillers,
                'boutique' => $boutique,
            ));
        }
    }

    /**
     * @param Conseiller_boutique $conseiller_boutique
     */
    private function listAndShowSecurity($conseiller_boutique = null)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a CONSEILLER or BOUTIQUE role
        $this->denyAccessUnlessGranted(['ROLE_CONSEILLER', 'ROLE_BOUTIQUE'], null, 'Unable to access this page!');
        /* ensure that the user is logged in  # granted even through remembering cookies
        *  and that the one is the owner
        */
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw $this->createAccessDeniedException();
        }
        //Pour afficher les details des boutique affiliés
        if (null !== $conseiller_boutique) {
            $user = $this->getUser();
            $conseiller = $conseiller_boutique->getConseiller()->getUtilisateur();
            $proprietaire = $conseiller_boutique->getBoutique()->getProprietaire();
            $gerant = $conseiller_boutique->getBoutique()->getGerant();
            if ($conseiller !== $user && $user !== $gerant && $user !== $proprietaire) {
                throw $this->createAccessDeniedException();
            }
        }
        //----------------------------------------------------------------------------------------
    }

    /**
     * Creates a new conseiller_boutique entity.
     * @param Request $request
     * @param Boutique $boutique
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request, Boutique $boutique = null)
    {
        $this->createSecurity($boutique);
        /** @var Conseiller_boutique $conseiller_boutique */
        $conseiller_boutique = TradeFactory::getTradeProvider("conseiller_boutique");
        $em = $this->getDoctrine()->getManager();
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        if (null !== $boutique) {
            $conseiller_boutique->setBoutique($boutique);
            $conseiller_boutique->setConseiller($user->getProfileConseiller());
            $em->persist($conseiller_boutique);
            $em->flush();
            return $this->redirectToRoute('apm_marketing_conseiller_boutique_index');
        } else {
            $form = $this->createForm('APM\MarketingDistribueBundle\Form\Conseiller_boutiqueType', $conseiller_boutique);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->createSecurity($form->get('boutique')->getData());
                $conseiller_boutique->setConseiller($user->getProfileConseiller());
                $em = $this->getDoctrine()->getManager();
                $em->persist($conseiller_boutique);
                $em->flush();

                return $this->redirectToRoute('apm_marketing_conseiller_boutique_show', array('id' => $conseiller_boutique->getId()));
            }

            return $this->render('APMMarketingDistribueBundle:conseiller_boutique:new.html.twig', array(
                'conseiller_boutique' => $conseiller_boutique,
                'form' => $form->createView(),
            ));
        }
    }

    /**
     * @param Boutique $boutique
     */
    private function createSecurity($boutique)
    {
        //---------------------------------security-----------------------------------------------
        // Vérifier que l'utilisateur courant est bel et bien le conseiller
        $this->denyAccessUnlessGranted('ROLE_CONSEILLER', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        /** @var Utilisateur_avm $user */
        $user = $this->getUser();
        $conseiller = $user->getProfileConseiller();
        $oldConseiller = null;
        if ($boutique && null !== $conseiller) {//l'enregistrement devrait être unique
            $em = $this->getDoctrine()->getManager();
            $oldConseiller = $em->getRepository('APMMarketingDistribueBundle:Conseiller_boutique')
                ->findOneBy(['conseiller' => $conseiller, 'boutique' => $boutique]);
        }

        if (null === $conseiller || null !== $oldConseiller) throw $this->createAccessDeniedException();
        //----------------------------------------------------------------------------------------
    }

    /**
     * Finds and displays a conseiller_boutique entity.
     * @param Conseiller_boutique $conseiller_boutique
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Conseiller_boutique $conseiller_boutique)
    {
        $this->listAndShowSecurity($conseiller_boutique);
        $deleteForm = $this->createDeleteForm($conseiller_boutique);

        return $this->render('APMMarketingDistribueBundle:conseiller_boutique:show.html.twig', array(
            'conseiller_boutique' => $conseiller_boutique,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a conseiller_boutique entity.
     *
     * @param Conseiller_boutique $conseiller_boutique The conseiller_boutique entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Conseiller_boutique $conseiller_boutique)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apm_marketing_conseiller_boutique_delete', array('id' => $conseiller_boutique->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing conseiller_boutique entity.
     * @param Request $request
     * @param Conseiller_boutique $conseiller_boutique
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Conseiller_boutique $conseiller_boutique)
    {
        $this->editAndDeleteSecurity($conseiller_boutique, $conseiller_boutique->getConseiller());

        $deleteForm = $this->createDeleteForm($conseiller_boutique);
        $editForm = $this->createForm('APM\MarketingDistribueBundle\Form\Conseiller_boutiqueType', $conseiller_boutique);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->editAndDeleteSecurity($conseiller_boutique, $conseiller_boutique->getConseiller());
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('apm_marketing_conseiller_boutique_show', array('id' => $conseiller_boutique->getId()));
        }

        return $this->render('APMMarketingDistribueBundle:conseiller_boutique:edit.html.twig', array(
            'conseiller_boutique' => $conseiller_boutique,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @param Conseiller_boutique $conseiller_boutique
     * @param Conseiller $conseiller
     */
    private function editAndDeleteSecurity($conseiller_boutique, $conseiller)
    {
        //---------------------------------security-----------------------------------------------
        // Unable to access the controller unless you have a CONSEILLER role
        $this->denyAccessUnlessGranted('ROLE_CONSEILLER', null, 'Unable to access this page!');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
            throw $this->createAccessDeniedException();

        $user = $this->getUser();
        if ($conseiller_boutique) {
            $conseiller = $conseiller_boutique->getConseiller()->getUtilisateur();
        } else {
            $grantedUser = $conseiller->getUtilisateur();
        }

        if ($conseiller !== $user && $user !== $grantedUser) {
            throw $this->createAccessDeniedException();
        }
        //----------------------------------------------------------------------------------------

    }

    /**
     * Deletes a conseiller_boutique entity.
     * @param Request $request
     * @param Conseiller_boutique $conseiller_boutique
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Conseiller_boutique $conseiller_boutique)
    {
        $this->editAndDeleteSecurity($conseiller_boutique, $conseiller_boutique->getConseiller());

        $form = $this->createDeleteForm($conseiller_boutique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->editAndDeleteSecurity($conseiller_boutique, $conseiller_boutique->getConseiller());
            $em = $this->getDoctrine()->getManager();
            $em->remove($conseiller_boutique);
            $em->flush();
        }

        return $this->redirectToRoute('apm_marketing_conseiller_boutique_index');
    }

    /**
     * @ParamConverter("conseiller", options={"mapping": {"conseiller_id":"id"}})
     * @ParamConverter("boutique", options={"mapping": {"boutique_id":"id"}})
     * @param Conseiller_boutique|null $conseiller_boutique
     * @param Boutique|null $boutique
     * @param Conseiller|null $conseiller
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteFromListAction(Conseiller_boutique $conseiller_boutique = null,
                                         Conseiller $conseiller = null, Boutique $boutique = null)
    {
        $this->editAndDeleteSecurity($conseiller_boutique, $conseiller);
        $em = $this->getDoctrine()->getManager();
        if (!$conseiller_boutique && $boutique && $conseiller) {
            $conseiller_boutique = $em->getRepository('APMMarketingDistribueBundle:Conseiller_boutique')
                ->findOneBy(['conseiller' => $conseiller, 'boutique' => $boutique]);
        }
        $em->remove($conseiller_boutique);
        $em->flush();

        return $this->redirectToRoute('apm_marketing_conseiller_boutique_index');
    }
}
