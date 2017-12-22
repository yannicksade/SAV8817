<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 19/02/2017
 * Time: 13:53
 */

namespace APM\VenteBundle\Factory;


use APM\VenteBundle\Entity\Boutique;
use APM\VenteBundle\Entity\Categorie;
use APM\VenteBundle\Entity\Offre;
use APM\VenteBundle\Entity\Rabais_offre;
use APM\VenteBundle\Entity\Remise;
use APM\VenteBundle\Entity\Suggestion_produit;
use APM\VenteBundle\Entity\Transaction;
use APM\VenteBundle\Entity\Transaction_produit;
use APM\CoreBundle\Trade\CodeGenerator;

abstract class TradeFactory
{

    /**
     * @param string $var
     * @return TradeFactory
     *
     * Tenter de créer une entité 3 fois
     */
    public static function getTradeProvider($var)
    {
        $n = 3;
        $length = 5;
        $i = 0;
        if ($var === "boutique") {
            $boutique = null;
            while ($i < $n && $boutique == null) {
                $boutique = new Boutique(CodeGenerator::getGenerator($length));
                $i++;
            }
            return $boutique;
        } else
            if ($var === "categorie") {
                $categorie = null;
                while ($i < $n && $categorie == null) {
                    $categorie = new Categorie(CodeGenerator::getGenerator($length));
                    $i++;
                }
                return $categorie;
            } else
                if ($var === "offre") {
                    $offre = null;
                    while ($i < $n && $offre == null) {
                        $offre = new Offre(CodeGenerator::getGenerator($length));
                        $i++;
                    }
                    return $offre;
                } else
                    if ($var === "rabais") {
                        $rabais = null;
                        while ($i < $n && $rabais == null) {
                            $rabais = new Rabais_offre(CodeGenerator::getGenerator($length));
                            $i++;
                        }
                        return $rabais;
                    } else
                        if ($var === "transaction") {
                            $transaction = null;
                            while ($i < $n && $transaction == null) {
                                $transaction = new Transaction(CodeGenerator::getGenerator($length));
                                $i++;
                            }
                            return $transaction;
                        } else
                            if ($var === "transaction_produit") {
                                $tr_produit = null;
                                while ($i < $n && $tr_produit == null) {
                                    $tr_produit = new Transaction_produit();
                                    $i++;
                                }
                                return $tr_produit;
                            }
        return null;
    }

}