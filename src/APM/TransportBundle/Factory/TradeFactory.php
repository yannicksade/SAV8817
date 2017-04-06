<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 19/02/2017
 * Time: 13:53
 */

namespace APM\TransportBundle\Factory;


use APM\CoreBundle\Trade\CodeGenerator;


use APM\TransportBundle\Entity\Livraison;
use APM\TransportBundle\Entity\Livreur_boutique;
use APM\TransportBundle\Entity\Profile_transporteur;
use APM\TransportBundle\Entity\Transporteur_zoneintervention;
use APM\TransportBundle\Entity\Zone_intervention;

abstract class TradeFactory
{

    /**
     * @param string $var
     * @return TradeFactory
     * Tenter de créer une entité 3 fois
     */
    public static function getTradeProvider($var)
    {
        $n = 3;
        $length = 5;
        $i = 0;
        if ($var === "livraison") {
            $livraison = null;
            while ($i < $n && $livraison == null) {
                $livraison = new Livraison(CodeGenerator::getGenerator($length));
                $i++;
            }
            return $livraison;
        } else if ($var === "livreur_boutique") {
            $livreur_boutique = null;
            while ($i < $n && $livreur_boutique == null) {
                $livreur_boutique = new Livreur_boutique();
                $i++;
            }
            return $livreur_boutique;
        } else if ($var === "transporteur") {
            $transporteur = null;
            while ($i < $n && $transporteur == null) {
                $transporteur = new Profile_transporteur(CodeGenerator::getGenerator($length));
                $i++;
            }
            return $transporteur;
        } else if ($var === "zone_intervention") {
            $zone_intervention = null;
            while ($i < $n && $zone_intervention == null) {
                $zone_intervention = new Zone_intervention(CodeGenerator::getGenerator($length));
                $i++;
            }
            return $zone_intervention;
        } else if ($var === "transporteur_zoneIntervention") {
            $transporteur_zoneIntervention = null;
            while ($i < $n && $transporteur_zoneIntervention == null) {
                $transporteur_zoneIntervention = new Transporteur_zoneintervention();
                $i++;
            }
            return $transporteur_zoneIntervention;
        }
        return null;
    }

}