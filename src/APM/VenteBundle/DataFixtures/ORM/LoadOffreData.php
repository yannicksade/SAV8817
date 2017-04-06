<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 27/01/2017
 * Time: 11:35
 */

namespace APM\CoreBundle\DataFixtures\ORM;


use APM\VenteBundle\Factory\TradeFactory;
use APM\VenteBundle\Entity\Offre;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadOffreData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        /** @var Offre $offre */
        $offre = TradeFactory::getTradeProvider('offre');
        $offre->setVendeur($this->getReference('user-avm'));
        $offre->setCategorie($this->getReference('categorie'));
        $offre->setDesignation("ARRACHIDE");

        $manager->persist($offre);
        $manager->flush();
    }

    public function getOrder()
    {
        return 10;
    }
}