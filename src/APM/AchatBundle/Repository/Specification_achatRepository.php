<?php

namespace APM\AchatBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Specification_achatRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Specification_achatRepository extends EntityRepository
{
    public function getSpecificationsAvecTransactions()
    {
        $qb = $this->createQueryBuilder('s')
            ->leftJoin('s.createur', 't')
            ->addSelect('t');

        return $qb->getQuery()->getResult();
    }

}
