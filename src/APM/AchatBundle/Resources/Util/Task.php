<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 19/01/2017
 * Time: 11:03
 */
namespace APM\AchatBundle\Resources\Util;


class Task
{
    private $a;
    private $b;

    function __construct()
    {

    }

    public function setValues($a, $b)
    {
        $this->a = $a;
        $this->b = $b;;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->add();
    }

    public function add()
    {
        return $this->a + $this->b;
    }
}