<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Repository;

use App\Entity\Wahlkreistyp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Wahlkreistyp>
 */
class WahlkreistypRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wahlkreistyp::class);
    }

    public function save(Wahlkreistyp $type): void
    {
        $this->getEntityManager()->persist($type);
        $this->getEntityManager()->flush();
    }

    public function deleteByName(string $name): void
    {
        $qb = $this->createQueryBuilder('wt');
        $qb->delete(Wahlkreistyp::class, 'wt')
            ->where('wt.name = :name')
            ->setParameter('name', $name);
        $qb->getQuery()->execute();
    }

    /**
     * @return Wahlkreistyp[] Returns an array of Wahlkreistyp objects
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('wt')->orderBy('wt.name', 'ASC')
            ->getQuery()->getResult();
    }
}
