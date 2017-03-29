<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 19/02/2017
 * Time: 20:26
 */

namespace APM\CoreBundle\Trade;


class CodeGenerator
{
    /**
     * @param integer $length
     * @return string
     */
    public static function getGenerator($length)
    {
        $bytes = random_bytes($length);
        return strtoupper(bin2hex($bytes));
    }
}