<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 26/01/2017
 * Time: 10:19
 */

namespace APM\CoreBundle\DataFixtures\ORM;


use APM\MarketingDistribueBundle\Entity\Conseiller;
use APM\MarketingDistribueBundle\Factory\TradeFactory;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadConseillerData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {

        /** @var Conseiller $conseiller */
        $conseiller = TradeFactory::getTradeProvider("conseiller");
        $conseiller->setMatricule("MATAD125T");
        $conseiller->setCode("CONS125Test");
        $conseiller->setUtilisateur($this->getReference('user-avm'));

        $manager->persist($conseiller);
        $manager->flush();
        $this->addReference('conseiller', $conseiller);
    }

    public function getOrder()
    {
        return 11;
    }
}