<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 29/01/2017
 * Time: 20:53
 */

namespace APM\CoreBundle\DataFixtures\ORM;


use APM\MarketingDistribueBundle\Entity\Quota;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
Use Doctrine\Common\Persistence\ObjectManager;

class LoadQuotaData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {

        $commission = new Quota();
        $commission->setCode("Commission125Test");
        $commission->setBoutiqueProprietaire($this->getReference('boutique'));

        $manager->persist($commission);
        $manager->flush();
        $this->addReference('commission', $commission);
    }

    public function getOrder()
    {
        return 13;
    }
}