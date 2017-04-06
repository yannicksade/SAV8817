<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 26/01/2017
 * Time: 21:54
 */

namespace APM\CoreBundle\DataFixtures\ORM;


use APM\VenteBundle\Factory\TradeFactory;
use APM\VenteBundle\Entity\Transaction;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadTransactionData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        /** @var Transaction $transaction */
        $transaction = TradeFactory::getTradeProvider('transaction');
        $transaction->setAuteur($this->getReference('user-avm'));

        $manager->persist($transaction);
        $manager->flush();
    }

    public function getOrder()
    {
        return 11;
    }
}