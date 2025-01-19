<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Repository;

use App\Entity\Wahlkreis;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Wahlkreis>
 */
class WahlkreisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wahlkreis::class);
    }

    public function save(Wahlkreis $wahlkreis): void
    {
        $this->getEntityManager()->persist($wahlkreis);
        $this->getEntityManager()->flush();
    }

    public function getWahlkreiseFormatted(): array
    {
        $data = [];
        /* @var Wahlkreis $bundesland */
        foreach ($this->findAll() as $wahlkreis) {
            $name = \sprintf('%s (Nr. %s)', $wahlkreis->getName(), $wahlkreis->getNumber());
            $data[$name] = $wahlkreis->getId()->toString();
        }
        natsort($data);

        return $data;
    }

    public function getWahlkreiseByStateFormatted(string $state): array
    {
        $data = [];
        /* @var Wahlkreis $bundesland */
        foreach ($this->findBy(['state' => $state]) as $wahlkreis) {
            $name = \sprintf('%s (Nr. %s)', $wahlkreis->getName(), $wahlkreis->getNumber());
            $data[$wahlkreis->getId()->toString()] = $name;
        }
        natsort($data);

        return $data;
    }

    public function getStates(): array
    {
        $qb = $this->createQueryBuilder('w');
        $qb->select('w.state')
            ->orderBy('w.state', 'ASC')
            ->distinct();
        $data = [];
        foreach ($qb->getQuery()->getSingleColumnResult() as $bundesland) {
            $data[$bundesland] = $bundesland;
        }

        return $data;
    }
}
