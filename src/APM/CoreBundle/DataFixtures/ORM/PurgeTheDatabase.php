<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 28/01/2017
 * Time: 05:27
 */

namespace APM\CoreBundle\DataFixtures\ORM;


use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class PurgeTheDatabase implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {


    }
}