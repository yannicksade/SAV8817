<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 31/01/2017
 * Time: 18:09
 */

namespace APM\VenteBundle\Trade;

use APM\VenteBundle\Entity\Categorie;
use APM\VenteBundle\TradeAbstraction\TradeOperationInterface;
use APM\VenteBundle\Entity\Offre;
use APM\VenteBundle\Entity\Boutique;

class BoutiqueHandler implements TradeOperationInterface
{

    private $handler;

    function __construct(OperationHandler $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @Override
     * @param Offre $offre
     * @param Boutique $boutique
     */
    public function inserer($offre, $boutique)
    {
        $boutique->addOffre($offre);
        $offre->setBoutique($boutique);
    }

    /**
     * @param Offre $offre
     * @param Boutique $boutique
     */
    public function desinserer($offre, $boutique)
    {
        $boutique->removeOffre($offre);
        $offre->setBoutique(null);
    }

    /**
     * @param Categorie $categorie
     * @param Boutique $boutique
     */
    public function insererCategorie($categorie, $boutique)
    {
        $boutique->addCategory($categorie);
        $categorie->setBoutique($boutique);
    }


    /**
     * @param Categorie $categorie
     * @param Boutique $boutique
     */
    public function desinsererCategorie($categorie, $boutique)
    {
        $boutique->removeCategory($categorie);
        $categorie->setBoutique(null);
    }


    /**
     * @param Boutique $boutique
     * @param $users
     * publier une boutique revient à publier tous les catégorie et les offres qu'elle contient
     */
    public function publier($boutique, $users)
    {
        $users = null;
        $boutique->setPubliable(true);
        $liste_categories = $boutique->getCategories();
        foreach ($liste_categories as $categorie) {
            $categorie->setPubliable(true);
        }
        $liste_offres = $boutique->getOffres();
        foreach ($liste_offres as $offre) {
            $offre->setPubliable(true);
        }

    }

    /**
     * cette fonction transfere les droits de propriete sur la boutique
     * @param Boutique $boutique
     * @param $source
     * @param $destinataire
     * @param $destinataireNonAVM
     * @param $motif
     * @param $quantite
     * @param $montant
     * @param $ordre
     * @param $livraison
     * @param $manager
     * @internal param $quantite
     */
    public function transferer($boutique, $source, $destinataire, $destinataireNonAVM, $motif, $quantite, $montant, $ordre, $livraison, $manager)
    {

        $this->depublier($boutique, null);

        $operation = $this->handler->enregistrerTransaction(null, $source, $destinataireNonAVM, $motif, $montant, $quantite, $ordre, $livraison, $manager);
        $boutique->setProprietaire($destinataire);
        $operation->setBoutique($boutique);
        $boutique->addTransaction($operation);

    }

    /**
     * @param Boutique $boutique
     * @param $users
     */
    public function depublier($boutique, $users)
    {
        $users = null;
        $boutique->setPubliable(false);
        $liste_categories = $boutique->getCategories();
        foreach ($liste_categories as $categorie) {
            $categorie->setPubliable(false);
        }
        $liste_offres = $boutique->getOffres();
        foreach ($liste_offres as $offre) {
            $offre->setPubliable(false);
        }

    }

}