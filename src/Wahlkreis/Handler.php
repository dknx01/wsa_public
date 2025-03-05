<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Wahlkreis;

use App\Entity\User;
use App\Entity\Wahlkreis;
use App\Repository\WahlkreisRepository;
use App\User\Roles;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class Handler
{
    public function __construct(
        private WahlkreisRepository $wahlkreisRepository,
        private AuthorizationCheckerInterface $authorizationChecker,
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function getWahlkreiseByStateFormatted(string $state, UserInterface|User|null $user = null): array
    {
        $data = [];
        if ($user instanceof User && !$this->isAdminUser($user)) {
            foreach ($user->getWahlkreisPermission() as $wahlkreis) {
                if ($wahlkreis->getState() !== $state) {
                    continue;
                }
                $name = \sprintf('%s (Nr. %s)', $wahlkreis->getName(), $wahlkreis->getNumber());
                $data[$wahlkreis->getId()->toString()] = $name;
            }
        }
        if (empty($data)) {
            /* @var Wahlkreis $bundesland */
            foreach ($this->wahlkreisRepository->findBy(['state' => $state]) as $wahlkreis) {
                $name = \sprintf('%s (Nr. %s)', $wahlkreis->getName(), $wahlkreis->getNumber());
                $data[$wahlkreis->getId()->toString()] = $name;
            }
        }

        natsort($data);

        return $data;
    }

    private function isAdminUser(UserInterface|User $user): bool
    {
        return $this->authorizationChecker->isGranted(Roles::ROLE_ADMIN->name, $user);
    }

    /**
     * @return array<string, string>
     */
    public function getWahlkreiseFormatted(UserInterface|User|null $user = null): array
    {
        $data = [];
        if ($user instanceof User && !$this->isAdminUser($user)) {
            /* @var Wahlkreis $wahlkreis */
            foreach ($user->getWahlkreisPermission() as $wahlkreis) {
                $data = $this->formatWahlkreise($wahlkreis, $data);
            }
        }
        if (empty($data)) {
            /* @var Wahlkreis $wahlkreis */
            foreach ($this->wahlkreisRepository->findAll() as $wahlkreis) {
                $data = $this->formatWahlkreise($wahlkreis, $data);
            }
        }
        natsort($data);

        return $data;
    }

    /**
     * @param array<string, string> $data
     *
     * @return array<string, string>
     */
    private function formatWahlkreise(Wahlkreis $wahlkreis, array $data): array
    {
        $name = \sprintf('%s (Nr. %s)', $wahlkreis->getName(), $wahlkreis->getNumber());
        $data[$name] = $wahlkreis->getId()->toString();

        return $data;
    }

    /**
     * @return array<string, string>
     */
    public function getStates(UserInterface|User|null $user = null): array
    {
        $data = [];
        if ($user instanceof User && !$this->isAdminUser($user)) {
            foreach ($user->getWahlkreisPermission() as $wahlkreis) {
                if (!\array_key_exists($wahlkreis->getState(), $data)) {
                    $data[$wahlkreis->getState()] = $wahlkreis->getState();
                }
            }
        }
        if (empty($data)) {
            $data = $this->wahlkreisRepository->getStates();
        }
        natsort($data);

        return $data;
    }
}
