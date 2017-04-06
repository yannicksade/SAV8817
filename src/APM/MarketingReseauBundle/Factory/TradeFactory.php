<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 19/02/2017
 * Time: 13:53
 */

namespace APM\MarketingReseauBundle\Factory;


use APM\CoreBundle\Trade\CodeGenerator;
use APM\MarketingDistribueBundle\Entity\Conseiller;
use APM\MarketingReseauBundle\Entity\Reseau_conseillers;

abstract class TradeFactory
{
    /**
     * @param string $var
     * @return TradeFactory
     */
    public static function getTradeProvider($var)
    {
        $n = 3;
        $length = 5;
        $i = 0;
        if ($var === "reseau_conseillers" && Reseau_conseillers::$nombreInstanceReseau < Conseiller::MAX_INSTANCE_RESEAU) {
            $reseau_conseiller = null;
            while ($i < $n && $reseau_conseiller == null) {
                $reseau_conseiller = new Reseau_conseillers(CodeGenerator::getGenerator($length));
                $i++;
            }
            return $reseau_conseiller;
        }

        return null;
    }

}