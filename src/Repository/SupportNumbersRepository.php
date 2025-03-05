<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Repository;

use App\Entity\SupportNumbersDirektkandidat;
use App\Entity\SupportNumbersLandesliste;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SupportNumbersLandesliste>
 */
class SupportNumbersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SupportNumbersLandesliste::class);
    }

    public function save(SupportNumbersLandesliste $supportNumbers): void
    {
        $this->getEntityManager()->persist($supportNumbers);
        $this->getEntityManager()->flush();
    }

    /**
     * @return SupportNumbersLandesliste[]|SupportNumbersDirektkandidat[]
     */
    public function findByState(string $state): array
    {
        $qb = $this->createQueryBuilder('sn');
        $qb->where(
            $qb->expr()->like('sn.bundesland', ':state')
        )
            ->andWhere('sn.deleted = 0')
            ->setParameter('state', $state);

        return $qb->getQuery()->getResult();
    }

    public function delete(SupportNumbersLandesliste $supportNumbers): void
    {
        $this->getEntityManager()->remove($supportNumbers);
        $this->getEntityManager()->flush();
    }

    /**
     * @return array<string, string>
     */
    public function findAllBundeslaender(): array
    {
        return $this->createQueryBuilder('sn')
            ->select('sn.bundesland')
            ->distinct()
            ->where('sn.deleted = 0')
            ->orderBy('sn.bundesland', 'ASC')
            ->getQuery()->getSingleColumnResult();
    }

    /**
     * @return array<string, int>
     */
    public function getNumbersByName(string $name): array
    {
        $qb = $this->createQueryBuilder('sn');
        $qb->select(
            'SUM(sn.approved) AS approved, SUM(sn.unapproved) AS unapproved',
        );
        $qb->where('sn.name = :name')->setParameter('name', $name);
        $qb->andWhere('sn.deleted = 0');

        return array_map(static fn (?string $value) => (int) $value, $qb->getQuery()->getSingleResult());
    }

    /**
     * @throws ORMException
     */
    public function refresh(SupportNumbersLandesliste $supportNumber): void
    {
        $this->getEntityManager()->refresh($supportNumber);
    }
}
