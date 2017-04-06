<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 26/01/2017
 * Time: 17:44
 */

namespace APM\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;

class LoadEntityData implements FixtureInterface
{

    /**
     * Tous les dataFixtures créée dans le projet sont chargés ici
     * @param ObjectManager|EntityManagerInterface $manager
     */
    public function load(ObjectManager $manager)
    {
        $loader = new Loader();
//        $loader->addFixture(new LoadMessageData());// Or
        $loader->loadFromDirectory('src/APM/UserBundle');
        $loader->loadFromDirectory('src/APM/TransportBundle');
        $loader->loadFromDirectory('src/APM/VenteBundle');
        $loader->loadFromDirectory('src/APM/MarketingDistribueBundle');

        $purger = new ORMPurger($manager);
        $purger->purge();
        $em = $purger->getObjectManager();
        $executor = new ORMExecutor($em, $purger);
        $executor->execute($loader->getFixtures());
    }
}
