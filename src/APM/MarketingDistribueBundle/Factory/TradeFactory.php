<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 19/02/2017
 * Time: 13:53
 */

namespace APM\MarketingDistribueBundle\Factory;


use APM\MarketingDistribueBundle\Entity\Commissionnement;
use APM\MarketingDistribueBundle\Entity\Conseiller;
use APM\MarketingDistribueBundle\Entity\Conseiller_boutique;
use APM\MarketingDistribueBundle\Entity\Quota;

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
        if ($var === "commissionnement") {
            $commissionnement = null;
            while ($i < $n && $commissionnement == null) {
                $commissionnement = new Commissionnement(CodeGenerator::getGenerator($length));
                $i++;
            }
            return $commissionnement;
        } else if ($var === "conseiller") {
            $conseiller = null;
            while ($i < $n && $conseiller == null) {
                $conseiller = new Conseiller(CodeGenerator::getGenerator($length));
                $i++;
            }
            return $conseiller;
        } else if ($var === "conseiller_boutique") {
            $conseiller_boutique = null;
            while ($i < $n && $conseiller_boutique == null) {
                $conseiller_boutique = new Conseiller_boutique();
                $i++;
            }
            return $conseiller_boutique;
        } else if ($var === "quota") {
            $quota = null;
            while ($i < $n && $quota == null) {
                $quota = new Quota(CodeGenerator::getGenerator($length));
                $i++;
            }
            return $quota;
        }
        return null;
    }

}