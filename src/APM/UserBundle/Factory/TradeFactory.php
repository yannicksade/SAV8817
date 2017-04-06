<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 19/02/2017
 * Time: 13:53
 */

namespace APM\UserBundle\Factory;


use APM\CoreBundle\Trade\CodeGenerator;
use APM\UserBundle\Entity\Commentaire;
use APM\UserBundle\Entity\Communication;
use APM\UserBundle\Entity\Groupe_relationnel;
use APM\UserBundle\Entity\Individu_to_groupe;
use APM\UserBundle\Entity\Message;
use APM\UserBundle\Entity\Piece_jointe;

abstract class TradeFactory
{

    /**
     * @param string $var
     * @return TradeFactory | string
     *
     * Tenter de créer une entité 3 fois
     */
    public static function getTradeProvider($var)
    {
        $n = 3;
        $length = 5;
        $i = 0;
        if ($var === "commentaire") {
            $commentaire = null;
            while ($i < $n && $commentaire == null) {
                $commentaire = new Commentaire(CodeGenerator::getGenerator($length));
                $i++;
            }
            return $commentaire;
        } else if ($var === "communication") {
            $communication = null;
            while ($i < $n && $communication == null) {
                $communication = new Communication();
                $i++;
            }
            return $communication;
        } else if ($var === "groupe_relationnel") {
            $groupe_relationnel = null;
            while ($i < $n && $groupe_relationnel == null) {
                $groupe_relationnel = new Groupe_relationnel(CodeGenerator::getGenerator($length));
                $i++;
            }
            return $groupe_relationnel;
        } else if ($var === "individu_to_groupe") {
            $individu_groupe = null;
            while ($i < $n && $individu_groupe == null) {
                $individu_groupe = new Individu_to_groupe();
                $i++;
            }
            return $individu_groupe;

        }
        return null;
    }

}