<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 26/01/2017
 * Time: 10:19
 */

namespace APM\CoreBundle\DataFixtures\ORM;


use APM\VenteBundle\Entity\Boutique;
use APM\VenteBundle\Factory\TradeFactory;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadBoutiqueData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        /** @var Boutique $btq */
        $btq = TradeFactory::getTradeProvider('boutique');
        $btq->setDesignation("Ma Boutique");
        $btq->setProprietaire($this->getReference('user-avm'));
        $manager->persist($btq);
        $manager->flush();

        $this->addReference('boutique', $btq);
    }

    public function getOrder()
    {
        return 9;
    }
}