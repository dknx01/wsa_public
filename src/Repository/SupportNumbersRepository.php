<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Repository;

use App\Entity\SupportNumbersLandesliste;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
}
