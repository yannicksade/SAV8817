<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 31/01/2017
 * Time: 18:50
 */

namespace APM\VenteBundle\Controller;


use APM\AchatBundle\Entity\Specification_achat;
use APM\TransportBundle\Entity\Livraison;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\{
    Boutique, Categorie, Offre
};
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\{
    Request, Response
};
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class TradeController extends Controller
{

    /**************************************** INSERTION/DESINSERTION ******************************************/

    /**
     * @ParamConverter("boutique", options={"mapping": {"boutique_id":"id"}})
     * @param Offre $offre
     * @param Boutique $boutique
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function insererOffreDansBoutiqueAction(Offre $offre, Boutique $boutique)
    {
        $this->get('apm_vente.boutique')->inserer($offre, $boutique);
        $em = $this->getEM();
        $em->flush();

        $boutique->getOffres();

        return $this->render('APMVenteBundle:boutique:show.html.twig', array(
            'boutique' => $boutique,
            'delete_form' => null
        ));
    }

    private function getEM()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * @ParamConverter("boutique", options={"mapping": {"boutique_id":"id"}})
     * @param Offre $offre
     * @param Boutique $boutique
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function desinsererOffreDansBoutiqueAction(Offre $offre, Boutique $boutique)
    {
        $this->get('apm_vente.boutique')->desinserer($offre, $boutique);
        $em = $this->getEM();
        $em->flush();

        $boutique->getOffres();

        return $this->render('APMVenteBundle:boutique:show.html.twig', array(
            'boutique' => $boutique,
            'delete_form' => null
        ));
    }

    /**
     * @ParamConverter("categorie", options={"mapping": {"categorie_id":"id"}})
     * @param Offre $offre
     * @param Categorie $categorie
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function insererOffreDansCategorieAction(Offre $offre, Categorie $categorie)
    {
        $this->get('apm_vente.categorie')->inserer($offre, $categorie);
        $em = $this->getEM();
        $em->flush();

        $categorie->getOffres();
        return $this->render('APMVenteBundle:categorie:show.html.twig', array(
            'categorie' => $categorie,
            'delete_form' => null
        ));
    }

    /**
     * @ParamConverter("categorie", options={"mapping": {"categorie_id":"id"}})
     * @param Offre $offre
     * @param Categorie $categorie
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function desinsererOffreDansCategorieAction(Offre $offre, Categorie $categorie)
    {
        $this->get('apm_vente.categorie')->desinserer($offre, $categorie);
        $em = $this->getEM();
        $em->flush();

        $categorie->getOffres();
        return $this->render('APMVenteBundle:categorie:show.html.twig', array(
            'categorie' => $categorie,
            'delete_form' => null
        ));
    }

    /**
     * @ParamConverter("categorieParent", options={"mapping": {"categorieParent_id":"id"}})
     * @param Categorie $categorie
     * @param Categorie $categorieParent
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function insererCategorieParentAction(Categorie $categorie, Categorie $categorieParent)
    {
        $this->get('apm_vente.categorie')->insererCategorie($categorie, $categorieParent);
        $em = $this->getEM();
        $em->flush();

        $categorie->getCategorieCourante();
        return $this->render('APMVenteBundle:categorie:show.html.twig', array(
            'categorie' => $categorie,
            'delete_form' => null //pour tester
        ));
    }

    /**
     * @ParamConverter("categorieParent", options={"mapping": {"categorieParent_id":"id"}})
     * @param Categorie $categorie
     * @param Categorie $categorieParent
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function desinsererCategorieParentAction(Categorie $categorie, Categorie $categorieParent)
    {
        $this->get('apm_vente.categorie')->desinsererCategorie($categorie, $categorieParent);
        $em = $this->getEM();
        $em->flush();

        $categorie->getCategorieCourante();
        return $this->render('APMVenteBundle:categorie:show.html.twig', array(
            'categorie' => $categorie,
            'delete_form' => null //pour tester
        ));
    }

    /**
     * @param Categorie $categorie
     * @param Boutique $boutique
     * @return \Symfony\Component\HttpFoundation\Response
     * @ParamConverter("boutique", options={"mapping": {"boutique_id":"id"}})
     */
    public function insererCategorieDansBoutiqueAction(Categorie $categorie, Boutique $boutique)
    {
        $this->get('apm_vente.boutique')->insererCategorie($categorie, $boutique);
        $em = $this->getEM();
        $em->flush();

        $boutique->getCategories();
        return $this->render('APMVenteBundle:boutique:show.html.twig', array(
            'boutique' => $boutique,
            'delete_form' => null //just for the tests
        ));
    }

    /**
     * @param Categorie $categorie
     * @param Boutique $boutique
     * @return \Symfony\Component\HttpFoundation\Response
     * @ParamConverter("boutique", options={"mapping": {"boutique_id":"id"}})
     */
    public function desinsererCategorieDansBoutiqueAction(Categorie $categorie, Boutique $boutique)
    {
        $this->get('apm_vente.boutique')->desinsererCategorie($categorie, $boutique);
        $em = $this->getEM();
        $em->flush();

        $boutique->getCategories();
        return $this->render('APMVenteBundle:boutique:show.html.twig', array(
            'boutique' => $boutique,
            'delete_form' => null //just for the tests
        ));
    }

    /*******************************************PUBLICATION/DEPUBLICATION*****************************************
     * @param Boutique $boutique
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function publierBoutiqueAction(Boutique $boutique)
    {
        $this->get('apm_vente.boutique')->publier($boutique, null);
        $this->getEM()->flush();

        $liste_boutiques = $this->getEM()->getRepository('APMVenteBundle:Boutique')->findAll();
        return $this->render('APMVenteBundle:boutique:index.html.twig', array(
            'boutiques' => $liste_boutiques
        ));
    }

    public function depublierBoutiqueAction(Boutique $boutique)
    {
        $this->get('apm_vente.boutique')->depublier($boutique, null);
        $this->getEM()->flush();

        $liste_boutiques = $this->getEM()->getRepository('APMVenteBundle:Boutique')->findAll();
        return $this->render('APMVenteBundle:boutique:index.html.twig', array(
            'boutiques' => $liste_boutiques
        ));
    }

    /**
     * @param Categorie $categorie
     * @param Boutique $boutique
     * @return \Symfony\Component\HttpFoundation\Response
     * @ParamConverter("boutique", options={"mapping": {"boutique_id"="id"}})
     */
    public function publierCategorieAction(Categorie $categorie, Boutique $boutique)
    {
        $this->get('apm_vente.categorie')->publier($categorie, null);
        $this->getEM()->flush();

        $liste_categories = $boutique->getCategories();
        return $this->render('APMVenteBundle:categorie:index.html.twig', array(
            'categories' => $liste_categories
        ));
    }

    /**
     * @param Categorie $categorie
     * @param Boutique $boutique
     * @return \Symfony\Component\HttpFoundation\Response
     * @ParamConverter("boutique", options={"mapping": {"boutique_id"="id"}})
     */
    public function depublierCategorieAction(Categorie $categorie, Boutique $boutique)
    {
        $this->get('apm_vente.categorie')->depublier($categorie, null);
        $this->getEM()->flush();

        $liste_categories = $boutique->getCategories();
        return $this->render('APMVenteBundle:categorie:index.html.twig', array(
            'categories' => $liste_categories
        ));
    }

    /**
     * @param Request $request
     * @param Offre $offre
     * @param Utilisateur_avm $utilisateur
     * @return Response
     * @ParamConverter("utilisateur", options={"mapping": {"user_id":"id"}})
     */
    public function restreindreOffreAUtilisateurAction(Request $request, Offre $offre, Utilisateur_avm $utilisateur)
    {
        $offre->setModeVente(3);
        $form = $this->createForm('APM\VenteBundle\Form\Type\UsersPromptType');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $liste_utilisateurs = $form->get('utilisateurs')->getData();
            foreach ($liste_utilisateurs as $user) {
                $this->get('apm_vente.offre')->publier($offre, $user);
            }
            $em = $this->getEM();
            $em->flush();
            $liste_offres = $utilisateur->getUtilisateurOffres();
            return $this->render('APMVenteBundle:offre:index.html.twig', array(
                'offres' => $liste_offres
            ));
        }
        return $this->render('APMVenteBundle:prompt:prompt.html.twig', array(
            'form' => $form->createView()
        ));

    }

    /**
     * @param Request $request
     * @param Offre $offre
     * @param Utilisateur_avm $utilisateur
     * @return Response
     * @internal param Utilisateur_avm $user
     * @ParamConverter("utilisateur", options={"mapping": {"user_id":"id"}})
     */
    public function exclureOffreAUtilisateurAction(Request $request, Offre $offre, Utilisateur_avm $utilisateur)
    {

        $form = $this->createForm('APM\VenteBundle\Form\Type\UsersPromptType');
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //if(3==$offre->getModeVente()) {
            $liste_utilisateurs = $form->get('utilisateurs')->getData();
            foreach ($liste_utilisateurs as $user) {
                $this->get('apm_vente.offre')->depublier($offre, $user);
            }
            $em = $this->getEM();
            $em->flush();
            $liste_offres = $utilisateur->getUtilisateurOffres();
            return $this->render('APMVenteBundle:offre:index.html.twig', array(
                'offres' => $liste_offres
            ));
            // }
        }

        return $this->render('APMVenteBundle:prompt:prompt.html.twig', array(
            'form' => $form->createView()
        ));

    }

    /**
     * @param Request $request
     * @param Offre $offre
     * @param Utilisateur_avm $source
     * @param Utilisateur_avm $destinataire
     * @param Specification_achat $ordre
     * @param Livraison $livraison
     * @return Response
     * @ParamConverter("source", options={"mapping": {"source_id":"id"}})
     * @ParamConverter("destinataire", options={"mapping": {"destinataire_id":"id"}})
     * @ParamConverter("ordre", options={"mapping": {"ordre_id":"id"}})
     * @ParamConverter("livraison", options={"mapping": {"livraison_id":"id"}})
     */
    public function transfererOffreAction(Request $request, Offre $offre, Utilisateur_avm $source, Utilisateur_avm $destinataire, Specification_achat $ordre, Livraison $livraison)
    {
        $form = $this->createForm('APM\VenteBundle\Form\Type\TransactionPromptType');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $motif = $form->get('motif')->getData(); //'donation';
            $destinataireNonAVM = $form->get('utilisateurNonAVM')->getData();
            $montant = $form->get('montant')->getData();
            $quantite = $form->get('quantite')->getData();
            $em = $this->getEM();
            $this->get('apm_vente.offre')->transferer($offre, $source, $destinataire, $destinataireNonAVM, $motif, $quantite, $montant, $ordre, $livraison, $em);
            $em->flush();

            return $this->redirectToRoute('apm_vente_transaction_index');
        }

        return $this->render('APMVenteBundle:prompt:prompt.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @param Request $request
     * @param Boutique $boutique
     * @param Utilisateur_avm $source
     * @param Utilisateur_avm $destinataire
     * @param Specification_achat $ordre
     * @param Livraison $livraison
     * @return Response
     * @ParamConverter("source", options={"mapping": {"source_id":"id"}})
     * @ParamConverter("destinataire", options={"mapping": {"destinataire_id":"id"}})
     * @ParamConverter("ordre", options={"mapping": {"ordre_id":"id"}})
     * @ParamConverter("livraison", options={"mapping": {"livraison_id":"id"}})
     */
    public function transfererBoutiqueAction(Request $request, Boutique $boutique, Utilisateur_avm $source, Utilisateur_avm $destinataire, Specification_achat $ordre, Livraison $livraison)
    {
        $form = $this->createForm('APM\VenteBundle\Form\Type\TransactionPromptType');
        $form->remove('quantite');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $motif = $form->get('motif')->getData(); //'donation';
            $destinataireNonAVM = $form->get('utilisateurNonAVM')->getData();
            $montant = $form->get('montant')->getData();
            $quantite = null;
            $em = $this->getEM();
            $this->get('apm_vente.boutique')->transferer($boutique, $source, $destinataire, $destinataireNonAVM, $motif, $quantite, $montant, $ordre, $livraison, $em);
            $em->flush();

            return $this->redirectToRoute('apm_vente_transaction_index');
        }

        return $this->render('APMVenteBundle:prompt:prompt.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @param Request $request
     * @param Categorie $categorie
     * @param Utilisateur_avm $source
     * @param Utilisateur_avm $destinataire
     * @param Specification_achat $ordre
     * @param Livraison $livraison
     * @return Response
     * @ParamConverter("source", options={"mapping": {"source_id":"id"}})
     * @ParamConverter("destinataire", options={"mapping": {"destinataire_id":"id"}})
     * @ParamConverter("ordre", options={"mapping": {"ordre_id":"id"}})
     * @ParamConverter("livraison", options={"mapping": {"livraison_id":"id"}})
     */
    public function transfererCategorieAction(Request $request, Categorie $categorie, Utilisateur_avm $source, Utilisateur_avm $destinataire, Specification_achat $ordre, Livraison $livraison)
    {
        $form = $this->createForm('APM\VenteBundle\Form\Type\TransactionPromptType');
        $form->remove('quantite');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $motif = $form->get('motif')->getData(); //'donation';
            $destinataireNonAVM = $form->get('utilisateurNonAVM')->getData();
            $montant = $form->get('montant')->getData();
            $quantite = null;
            $em = $this->getEM();
            $this->get('apm_vente.categorie')->transferer($categorie, $source, $destinataire, $destinataireNonAVM, $motif, $quantite, $montant, $ordre, $livraison, $em);
            $em->flush();

            return $this->redirectToRoute('apm_vente_transaction_index');
        }

        return $this->render('APMVenteBundle:prompt:prompt.html.twig', array(
            'form' => $form->createView()
        ));
    }


}