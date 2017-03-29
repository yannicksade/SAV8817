<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 26/01/2017
 * Time: 11:03
 */

namespace APM\CoreBundle\DataFixtures\ORM;

use APM\TransportBundle\Entity\Profile_transporteur;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadTransporteurData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $transporteur = new Profile_transporteur();
        $transporteur->setCode("TRP120Test");
        $transporteur->setMatricule("TRP120Test");
        $transporteur->setUtilisateur($this->getReference('user-avm'));
        $manager->persist($transporteur);
        $manager->flush();
        $this->addReference('transporteur', $transporteur);
    }

    public function getOrder()
    {
        return 14;
    }
}