<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Repository;

use App\Entity\Wahlkreis;
use App\Entity\WahlkreisNumber;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\UuidInterface;

/**
 * @extends ServiceEntityRepository<WahlkreisNumber>
 */
class WahlkreisNumberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WahlkreisNumber::class);
    }

    public function save(WahlkreisNumber $wahlkreisNumber): void
    {
        $this->getEntityManager()->persist($wahlkreisNumber);
        $this->getEntityManager()->flush();
    }

    public function refresh(WahlkreisNumber $wahlkreisNumber): void
    {
        $this->getEntityManager()->refresh($wahlkreisNumber);
    }

    /**
     * @return array<string, int>
     */
    public function getNumbersByName(?string $name): array
    {
        $qb = $this->createQueryBuilder('wn');
        $qb->select(
            'SUM(wn.approved) AS approved, SUM(wn.unapproved) AS unapproved',
        );
        $qb->where('wn.name = :name')->setParameter('name', $name);
        $qb->andWhere('wn.deleted = 0');

        return array_map(static fn (?string $value) => (int) $value, $qb->getQuery()->getSingleResult());
    }

    /**
     * @return WahlkreisNumber[]
     */
    public function findByState(string $state): array
    {
        $qb = $this->createQueryBuilder('wn');
        $qb->innerJoin(Wahlkreis::class, 'wk', 'WITH', 'wk.id = wn.wahlkreis')
            ->where(
                $qb->expr()->like('wk.state', ':state')
            )
            ->andWhere('wn.deleted = 0')
            ->setParameter('state', $state);

        return $qb->getQuery()->getResult();
    }

    /**
     * @return array<array-key, WahlkreisNumber>
     */
    public function findAllActive(): array
    {
        $qb = $this->createQueryBuilder('wn');
        $qb->innerJoin('wn.wahlkreis', 'wk', 'WITH', 'wk.id = wn.wahlkreis');
        $qb->where('wn.deleted = 0');
        $qb->orderBy('wk.state', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * @return array<string, mixed>
     */
    public function getCurrentRawData(UuidInterface $id): array
    {
        $qb = $this->createQueryBuilder('wn');
        $qb->select('wn.comment', 'wn.approved', 'wn.unapproved')
            ->where('wn.id = :id')
            ->setParameter('id', $id);

        return $qb->getQuery()->getSingleResult();
    }

    /**
     * @return array<array-key, WahlkreisNumber>
     */
    public function findAllOrdered(): array
    {
        $qb = $this->createQueryBuilder('wn');
        $qb->innerJoin('wn.wahlkreis', 'wk', 'WITH', 'wk.id = wn.wahlkreis');
        $qb->orderBy('wk.state', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
