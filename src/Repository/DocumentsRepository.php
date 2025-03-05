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

    /**
     * @return Document[]
     */
    public function findAllByState(string $state): array
    {
        $qb = $this->createQueryBuilder('d');
        $qb->where('d.state = :state');
        $qb->setParameter('state', $state);

        return $qb->getQuery()->getResult();
    }

    /**
     * @return array<string, string>
     */
    public function findAllBundeslaender(): array
    {
        return $this->createQueryBuilder('d')
            ->select('d.state')
            ->orderBy('d.state', 'ASC')
            ->distinct()
            ->getQuery()->getSingleColumnResult();
    }

    /**
     * @return Document[]
     */
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
