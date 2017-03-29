<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 31/01/2017
 * Time: 17:48
 */
namespace APM\VenteBundle\TradeAbstraction;

interface TradeOperationInterface
{
    public function inserer($var1, $var2);

    public function desinserer($var1, $var2);

    public function publier($var1, $var2);

    public function depublier($var1, $var2);

    public function transferer($var1, $var2, $var3, $var4, $var5, $var6, $var7, $var8, $var9, $var10);
}