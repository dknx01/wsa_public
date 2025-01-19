<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Repository;

use App\Entity\Verbandsseiten;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Verbandsseiten>
 */
class VerbandsseitenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Verbandsseiten::class);
    }

    public function save(Verbandsseiten $seite): void
    {
        $this->getEntityManager()->persist($seite);
        $this->getEntityManager()->flush();
    }
}
