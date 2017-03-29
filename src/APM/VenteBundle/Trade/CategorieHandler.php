<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 31/01/2017
 * Time: 19:50
 */

namespace APM\VenteBundle\Trade;


use APM\AchatBundle\Entity\Specification_achat;
use APM\TransportBundle\Entity\Livraison;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Categorie;
use APM\VenteBundle\Entity\Offre;

use APM\VenteBundle\TradeAbstraction\TradeOperationInterface;
use Doctrine\Common\Persistence\ObjectManager;


class CategorieHandler implements TradeOperationInterface
{

    private $handler;

    function __construct(OperationHandler $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @Override
     * @param Offre $offre
     * @param Categorie $categorie
     * @return Categorie
     */
    public function inserer($offre, $categorie)
    {
        $categorie->addOffre($offre);
        $offre->setCategorie($categorie);
    }

    /**
     * @Override
     * @param Offre $offre
     * @param Categorie $categorie
     */
    public function desinserer($offre, $categorie)
    {
        $categorie->removeOffre($offre);
        $offre->setCategorie(null);
    }

    public function insererCategorie(Categorie $categorie, Categorie $categorieParent)
    {
        $categorie->setCategorieCourante($categorieParent);
    }

    public function desinsererCategorie(Categorie $categorie)
    {
        $categorie->setCategorieCourante(null);
    }

    /**
     * @param Categorie $categorie
     * @param $users
     * @return Categorie
     * publier une categorie revient à publier tous les offres qu'elle contient
     */
    public function publier($categorie, $users)
    {
        $users = null;
        $categorie->setPubliable(true);
        $liste_offres = $categorie->getOffres();
        foreach ($liste_offres as $offre) {
            $offre->setPubliable(true);
        }
    }

    /**
     * @param Categorie $categorie
     * @param Utilisateur_avm $source
     * @param Utilisateur_avm $destinataire
     * @param string $destinataireNonAVM
     * @param string $motif
     * @param $quantite
     * @param decimal $montant
     * @param Specification_achat $ordre
     * @param Livraison $livraison
     * @param ObjectManager $manager
     * Transferer une Categorie et enregistrer la transaction
     */
    public function transferer($categorie, $source, $destinataire, $destinataireNonAVM, $motif, $quantite, $montant, $ordre, $livraison, $manager)
    {

        $this->depublier($categorie, null);
        $categorie->setCategorieCourante(null);
        $liste_offres = $categorie->getOffres();
        foreach ($liste_offres as $offre) {
            $offre->setVendeur($destinataire);
        }
        $categorie->setBoutique(null);

        $this->handler->enregistrerTransaction(null, $source, $destinataireNonAVM, $motif, $montant, $quantite, $ordre, $livraison, $manager);
    }

    /**
     * @param Categorie $categorie
     * @param $users
     * @return Categorie
     */
    public function depublier($categorie, $users)
    {
        $users = null;
        $categorie->setPubliable(false);
        $liste_offres = $categorie->getOffres();
        foreach ($liste_offres as $offre) {
            $offre->setPubliable(false);
        }
    }


}