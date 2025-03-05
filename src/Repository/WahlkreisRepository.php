<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Repository;

use App\Entity\User;
use App\Entity\Wahlkreis;
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
}
