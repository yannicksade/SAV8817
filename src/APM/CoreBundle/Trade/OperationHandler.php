<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 17/02/2017
 * Time: 18:24
 */

namespace APM\CoreBundle\Trade;


use APM\AchatBundle\Entity\Specification_achat;
use APM\TransportBundle\Entity\Livraison;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Offre;
use APM\VenteBundle\Entity\Transaction;
use APM\CoreBundle\TradeAbstraction\Trade;
use APM\VenteBundle\Factory\TradeFactory;
use Doctrine\Common\Persistence\ObjectManager;
use APM\VenteBundle\Entity\Transaction_produit;

class OperationHandler
{
    /**
     * @var Transaction
     */
    private $operation;

    /**
     * @param Offre $offre
     * @param Utilisateur_avm $source
     * @param string $destinataireNonAVM
     * @param string $motif
     * @param decimal $montant
     * @param $quantite
     * @param Specification_achat $ordre
     * @param Livraison $livraison
     * @param ObjectManager $manager
     * @return Transaction Cette fonction entregistre une opération portant sur un objet sans changement du proprietaire
     *
     * Cette fonction entregistre une opération portant sur un objet sans changement du proprietaire
     */
    public function enregistrerTransaction($offre, $source, $destinataireNonAVM, $motif, $montant, $quantite, $ordre, $livraison, $manager)
    {

        $this->operation = TradeFactory::getTradeProvider('transaction');

        $this->operation->setNature($motif);
        $this->operation->setStatut(0);
        $this->operation->setMontant($montant);
        $this->operation->setDestinataireNonAvm($destinataireNonAVM);
        $source->addTransaction($this->operation);
        $this->operation->setAuteur($source);

        if (null !== $quantite && null !== $offre) {
            /** @var Transaction_produit $transactionProduit */
            $transactionProduit = TradeFactory::getTradeProvider('transaction_produit');
            $transactionProduit->setQuantite($quantite);
            $transactionProduit->setProduit($offre);
            $this->operation->addTransactionProduit($transactionProduit);
            $transactionProduit->setTransaction($this->operation);
            $manager->persist($transactionProduit);
        }

        if (null !== $ordre) {
            $ordre->addOperation($this->operation);
            $this->operation->setOrdre($ordre);
        }
        if (null !== $livraison) {
            $livraison->addOperation($this->operation);
            $this->operation->setLivraison($livraison);
        }
        $manager->persist($this->operation);
        return $this->operation;
    }

    public function getOperation()
    {
        return $this->operation;
    }
}