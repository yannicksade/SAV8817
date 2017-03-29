<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 26/01/2017
 * Time: 17:44
 */

namespace APM\CoreBundle\DataFixtures\ORM;

use APM\VenteBundle\Entity\Categorie;
use APM\VenteBundle\TradeAbstraction\Trade;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadCategorieData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        /** @var Categorie $categorie */
        $categorie = Trade::getTradeProvider('categorie');
        $categorie->setDesignation("BASE CATEGORY");
        $categorie->setBoutique($this->getReference('boutique'));
        $manager->persist($categorie);
        $manager->flush();
        $this->addReference('categorie', $categorie);
    }

    public function getOrder()
    {
        return 10;
    }
}