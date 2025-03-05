<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Repository;

use App\Entity\Statistic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Statistic>
 */
class StatisticRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Statistic::class);
    }

    public function save(Statistic $data): void
    {
        $this->getEntityManager()->persist($data);
        $this->getEntityManager()->flush();
    }

    /**
     * @return array<string, string>
     */
    public function findAllBundeslaender(): array
    {
        return $this->createQueryBuilder('s')
            ->select('s.bundesland')
            ->distinct()
            ->orderBy('s.bundesland', 'ASC')
            ->getQuery()->getSingleColumnResult();
    }

    public function findByStateAndArea(string $state, string $area): ?Statistic
    {
        return $this->createQueryBuilder('s')
            ->where('s.bundesland = :state')
            ->andWhere('s.name = :area')
            ->setParameter('state', $state)
            ->setParameter('area', $area)
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * @return Statistic[]
     */
    public function findByState(string $state): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.bundesland = :state')
            ->orderBy('s.name', 'DESC')
            ->setParameter('state', $state)
            ->getQuery()->getResult();
    }
}
