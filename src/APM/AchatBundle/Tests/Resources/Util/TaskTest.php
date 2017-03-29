<?php
namespace APM\AchatBundle\Tests\Resources\Util;

use APM\AchatBundle\Resources\Util\Task;

/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 18/01/2017
 * Time: 22:46
 */
class TaskTest extends \PHPUnit_Framework_TestCase
{
    private $calc;

    public function setUp()
    {
        $this->calc = new Task();
    }

    public function testAdd()
    {
        /*
         *NB: Ce test utilise une dépendance au service "apm_achat_test" qu'il faudra limiter avec les mocks.
         * si non utiliser les tests fonctionnels
         */
        $result = $this->calc->add();
        $this->assertEquals(5, $result);
    }

}
