<?php

namespace APM\VenteBundle\Repository;

use Doctrine\ORM\EntityRepository;


/**
 * CategorieRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategorieRepository extends EntityRepository
{
    public function getCategorieAvecCategorieParent()
    {

        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.categorieCourante', 'sc')
            ->addSelect('sc');

        return $qb->getQuery()->getResult();

    }
}