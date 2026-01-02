<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Repository;

use App\Entity\User;
use App\Entity\Wahlkreis;
use App\Entity\WahlkreisNumber;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

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

    /**
     * @return array<string, string>
     */
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

    /**
     * @return array<string, string>
     */
    public function getStatesByUser(UserInterface|User $user): array
    {
        $data = [];
        if (!$user instanceof User || $user->getWahlkreisPermission()->isEmpty()) {
            $qb = $this->createQueryBuilder('w');
            $qb->select('w.state')
                ->orderBy('w.state', 'ASC')
                ->distinct();
            foreach ($qb->getQuery()->getSingleColumnResult() as $bundesland) {
                $data[$bundesland] = $bundesland;
            }
        } else {
            foreach ($user->getWahlkreisPermission() as $wahlkreis) {
                if (!\array_key_exists($wahlkreis->getState(), $data)) {
                    $data[$wahlkreis->getState()] = $wahlkreis->getState();
                }
            }
        }

        return $data;
    }

    /**
     * @return Wahlkreis[]
     */
    public function findAllOrdered(): array
    {
        return $this->findBy([], ['name' => 'ASC']);
    }

    /**
     * @return array<string, string>
     */
    public function findAllStatesWithNumbers(): array
    {
        $qb = $this->createQueryBuilder('w');
        $qb->select('w.state')
            ->distinct()
            ->join(WahlkreisNumber::class, 'wn', 'WITH', 'w.id = wn.wahlkreis');
        $qb->where($qb->expr()->eq('wn.deleted', 0));
        $qb->andWhere('wn.deleted = 0');
        $qb->orderBy('w.state', 'ASC');

        return $qb->getQuery()->getSingleColumnResult();
    }
}
