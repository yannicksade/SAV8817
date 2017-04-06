<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 29/01/2017
 * Time: 20:43
 */

namespace APM\CoreBundle\DataFixtures\ORM;


use APM\MarketingDistribueBundle\Entity\Conseiller_boutique;
use APM\MarketingDistribueBundle\Factory\TradeFactory;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadConseillerBoutiqueData extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {

        /** @var Conseiller_boutique $conseillerBtq */
        $conseillerBtq = TradeFactory::getTradeProvider("conseiller_boutique");;
        $conseillerBtq->setConseiller($this->getReference('conseiller'));
        $conseillerBtq->setBoutique($this->getReference('boutique'));

        $manager->persist($conseillerBtq);
        $manager->flush();
        $this->addReference('conseiller-boutique', $conseillerBtq);
    }

    public function getOrder()
    {
        return 12;
    }
}