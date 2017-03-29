<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 19/02/2017
 * Time: 13:53
 */

namespace APM\VenteBundle\TradeAbstraction;


use APM\VenteBundle\Entity\Boutique;
use APM\VenteBundle\Entity\Categorie;
use APM\VenteBundle\Entity\Offre;
use APM\VenteBundle\Entity\Rabais_offre;
use APM\VenteBundle\Entity\Remise;
use APM\VenteBundle\Entity\Suggestion_produit;
use APM\VenteBundle\Entity\Transaction;
use APM\VenteBundle\Entity\Transaction_produit;
use APM\CoreBundle\Trade\CodeGenerator;

abstract class Trade
{

    /**
     * @param string $var
     * @return Trade
     */
    public static function getTradeProvider($var)
    {

        $code = CodeGenerator::getGenerator(5);
        $i = 0;
        if ($var === "boutique") {
            $boutique = null;
            while ($i < 3 && $boutique == null) {
                $boutique = new Boutique($code);
                $i++;
            }
            return $boutique;
        } else
            if ($var === "categorie") {
                $categorie = null;
                while ($i < 3 && $categorie == null) {
                    $categorie = new Categorie($code);
                    $i++;
                }
                return $categorie;
            } else
                if ($var === "offre") {
                    $offre = null;
                    while ($i < 3 && $offre == null) {
                        $offre = new Offre($code);
                        $i++;
                    }
                    return $offre;
                } else
                    if ($var === "rabais") {
                        $rabais = null;
                        while ($i < 3 && $rabais == null) {
                            $rabais = new Rabais_offre($code);
                            $i++;
                        }
                        return $rabais;
                    } else
                        if ($var === "remise") {
                            $remise = null;
                            while ($i < 3 && $remise == null) {
                                $remise = new Remise($code);
                                $i++;
                            }
                            return $remise;
                        } else
                            if ($var === "suggestion") {
                                $suggestion = null;
                                while ($i < 3 && $suggestion == null) {
                                    $suggestion = new Suggestion_produit($code);
                                    $i++;
                                }
                                return $suggestion;
                            } else
                                if ($var === "transaction") {
                                    $transaction = null;
                                    while ($i < 3 && $transaction == null) {
                                        $transaction = new Transaction($code);
                                        $i++;
                                    }
                                    return $transaction;
                                } else
                                    if ($var === "transaction_produit") {
                                        $tr_produit = null;
                                        while ($i < 3 && $tr_produit == null) {
                                            $tr_produit = new Transaction_produit($code);
                                            $i++;
                                        }
                                        return $tr_produit;
                                    }
        return null;
    }

}