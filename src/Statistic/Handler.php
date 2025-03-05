<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Statistic;

use App\Entity\Statistic;
use App\Entity\SupportNumbersDirektkandidat;
use App\Entity\SupportNumbersLandesliste;
use App\Entity\User;
use App\Entity\Wahlkreis;
use App\Repository\StatisticRepository;
use App\Repository\SupportNumbersRepository;
use App\User\Roles;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class Handler
{
    public function __construct(
        private StatisticRepository $statisticRepository,
        private SupportNumbersRepository $supportNumbersRepository,
        private AuthorizationCheckerInterface $authorizationChecker,
    ) {
    }

    /**
     * @return StatisticOverviewCollection<OverviewItem>
     */
    public function findAll(User|UserInterface|null $user): StatisticOverviewCollection
    {
        $data = new StatisticOverviewCollection();

        foreach ($this->statisticRepository->findAll() as $statistic) {
            $data->add(
                new OverviewItem(
                    $statistic,
                    $user && $this->overviewItemAccess($user, $statistic)
                )
            );
        }

        return $data;
    }

    private function overviewItemAccess(User|UserInterface|null $user, Statistic $statistic): bool
    {
        if (!$user instanceof User) {
            return false;
        }
        if ('Landesliste' === $statistic->getType()) {
            return $user->getWahlkreisPermission()->exists(fn (int $key, Wahlkreis $wahlkreis) => $wahlkreis->getState() === $statistic->getBundesland()
            );
        }

        return $user->getWahlkreisPermission()->exists(fn (int $key, Wahlkreis $wahlkreis) => str_starts_with($statistic->getName(), $wahlkreis->getName())
        );
    }

    private function overviewItemAccessSupportNumbers(User|UserInterface $user, SupportNumbersLandesliste|SupportNumbersDirektkandidat $supportNumbers): bool
    {
        if (!$user instanceof User) {
            return false;
        }
        if ($supportNumbers instanceof SupportNumbersDirektkandidat) {
            return $user->getWahlkreisPermission()->exists(fn (int $key, Wahlkreis $wahlkreis) => $supportNumbers->getWahlkreis()?->getId()->equals($wahlkreis->getId()));
        }

        return $user->getWahlkreisPermission()->exists(fn (int $key, Wahlkreis $wahlkreis) => $wahlkreis->getState() === $supportNumbers->getBundesland()
        );
    }

    /**
     * @return StatisticDataItem[]
     */
    public function findByState(string $state, UserInterface|User|null $user): array
    {
        $data = [];
        foreach ($this->supportNumbersRepository->findByState($state) as $supportNumber) {
            $id = ($user instanceof User) ? $supportNumber->getId() : null;

            if (null !== $id
                && !$this->authorizationChecker->isGranted(Roles::ROLE_ADMIN->name)
                && !$this->overviewItemAccessSupportNumbers($user, $supportNumber)
            ) {
                $id = null;
            }

            $data[] = new StatisticDataItem(
                $id,
                $supportNumber->getApproved(),
                $supportNumber->getUnapproved(),
                $supportNumber->getName(),
                $supportNumber->getCreatedBy()?->getEmail(),
                $supportNumber->getCreatedAt()?->format('Y-m-d H:i:s'),
                $supportNumber->getUpdatedAt()?->format('Y-m-d H:i:s'),
                $supportNumber->getType(),
                $supportNumber->getBundesland(),
                $supportNumber->getComment() ?? '',
            );
        }

        return $data;
    }
}
