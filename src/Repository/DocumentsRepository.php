<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Repository;

use App\Entity\Document;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Document>
 */
class DocumentsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Document::class);
    }

    public function save(Document $data): void
    {
        $this->getEntityManager()->persist($data);
        $this->getEntityManager()->flush();
    }

    public function findAllByState(string $state, string $type = 'Landesliste'): array
    {
        return $this->findBy(['state' => $state, 'type' => $type], ['name' => 'ASC']);
    }

    public function findAllBundeslaender(): array
    {
        return $this->createQueryBuilder('d')
            ->select('d.state')
            ->orderBy('d.state', 'ASC')
            ->distinct()
            ->getQuery()->getSingleColumnResult();
    }

    public function findByName(string $search): array
    {
        $qb = $this->createQueryBuilder('d');
        $qb->where('d.name LIKE :search')
            ->setParameter('search', '%'.$search.'%');

        return $qb->getQuery()->getResult();
    }

    public function refresh(Document $document): void
    {
        $this->getEntityManager()->refresh($document);
    }

    public function delete(Document $document): void
    {
        $this->getEntityManager()->remove($document);
        $this->getEntityManager()->flush();
    }
}
