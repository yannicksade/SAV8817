<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 19/02/2017
 * Time: 13:53
 */

namespace APM\AnimationBundle\Factory;


use APM\AnimationBundle\Entity\Base_documentaire;
use APM\CoreBundle\Trade\CodeGenerator;

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
        if ($var === "base_documentaire") {
            $base_documentaire = null;
            while ($i < $n && $base_documentaire == null) {
                $base_documentaire = new Base_documentaire(CodeGenerator::getGenerator($length));
                $i++;
            }
            return $base_documentaire;
        }

        return null;
    }

}