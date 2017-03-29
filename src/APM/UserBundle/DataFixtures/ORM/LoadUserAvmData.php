<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 26/01/2017
 * Time: 10:19
 */

namespace APM\CoreBundle\DataFixtures\ORM;


use APM\UserBundle\Entity\Utilisateur_avm;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadUserAvmData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {

        $user = new Utilisateur_avm();
        $user->setUsername("userTest1");
        $user->setEmail("test1@avm.com");
        $user->setCode("USR125T");
        $user->setNom("USER1 AVM");
        $user->setPassword('password');

        $manager->persist($user);
        $manager->flush();
        $this->addReference('user', $user);
    }

    public function getOrder()
    {
        return 1;
    }
}

class LoadUserAvm2Data extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {

        $user = new Utilisateur_avm();
        $user->setUsername("userTest2");
        $user->setEmail("test2@avm.com");
        $user->setCode("USR125T2");
        $user->setNom("USER2 AVM");
        $user->setPassword('test');
        $this->addReference('user-avm', $user);
        $manager->persist($user);
        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}