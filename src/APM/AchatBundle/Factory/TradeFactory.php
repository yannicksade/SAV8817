<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 19/02/2017
 * Time: 13:53
 */

namespace APM\AchatBundle\Factory;


use APM\AchatBundle\Entity\Groupe_offre;
use APM\AchatBundle\Entity\Service_apres_vente;
use APM\AchatBundle\Entity\Specification_achat;

use APM\CoreBundle\Trade\CodeGenerator;

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
        if ($var === "groupe_offre") {
            $groupe_offre = null;
            while ($i < $n && $groupe_offre == null) {
                $groupe_offre = new Groupe_offre(CodeGenerator::getGenerator($length));
                $i++;
            }
            return $groupe_offre;
        } else if ($var === "service_apres_vente") {
            $service_apres_vente = null;
            while ($i < $n && $service_apres_vente == null) {
                $service_apres_vente = new Service_apres_vente(CodeGenerator::getGenerator($length));
                $i++;
            }
            return $service_apres_vente;
        } else if ($var === "specification_achat") {
            $specification_achat = null;
            while ($i < $n && $specification_achat == null) {
                $specification_achat = new Specification_achat(CodeGenerator::getGenerator($length));
                $i++;
            }
            return $specification_achat;
        }
        return null;
    }

}