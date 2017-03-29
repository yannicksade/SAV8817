<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 26/01/2017
 * Time: 11:03
 */

namespace APM\CoreBundle\DataFixtures\ORM;

use APM\UserBundle\Entity\Message;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadMessage1Data extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $message = new Message();
        $message->setCode("MSG120Test");
        $manager->persist($message);
        $manager->flush();
    }

    public function getOrder()
    {
        return 5;
    }
}

class LoadMessage2Data extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $message = new Message();
        $message->setCode("MSG121Test");
        $manager->persist($message);
        $manager->flush();
    }

    public function getOrder()
    {
        return 6;
    }

}