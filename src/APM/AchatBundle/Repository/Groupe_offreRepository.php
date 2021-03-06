<?php

namespace APM\AchatBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Groupe_offreRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Groupe_offreRepository extends EntityRepository
{

    public function getGroupesOffresByOwner($user)
    {
        $qb = $this->createQueryBuilder('g')
            ->leftJoin('g.createur', 'c')
            ->addSelect('c');

        return $qb->getQuery()->getResult();
    }

    public function getGroupesOffresAvecOffres()
    {

        $qb = $this->createQueryBuilder('g')
            ->leftJoin('g.offres', 'o')
            ->addSelect('o');

        return $qb->getQuery()->getResult();
    }
}
