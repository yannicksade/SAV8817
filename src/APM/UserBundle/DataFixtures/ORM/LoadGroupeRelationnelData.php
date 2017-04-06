<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 27/01/2017
 * Time: 11:56
 */

namespace APM\CoreBundle\DataFixtures\ORM;


use APM\UserBundle\Entity\Groupe_relationnel;
use APM\UserBundle\Factory\TradeFactory;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadGroupeRelationnelData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {

        /** @var Groupe_relationnel $relationnel */
        $relationnel = TradeFactory::getTradeProvider("groupe_relationnel");
        $relationnel->setDesignation("GroupeTest1");
        $relationnel->setCode("GRP125T");
        $relationnel->setProprietaire($this->getReference('user-avm'));

        $manager->persist($relationnel);
        $manager->flush();
        $this->addReference('groupe-relationnel', $relationnel);
    }

    public function getOrder()
    {
        return 20;
    }
}