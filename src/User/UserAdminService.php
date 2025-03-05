<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\User;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\WahlkreisRepository;

readonly class UserAdminService
{
    public function __construct(
        private WahlkreisRepository $wahlkreisRepository,
        private UserRepository $userRepository,
    ) {
    }

    public function saveUser(User $user, EditDvo $userData): void
    {
        $user->setEmail($userData->email)
            ->setActive($userData->active)
            ->getWahlkreisPermission()->clear();
        foreach ($userData->wahlkreis as $wahlkreis => $active) {
            $user->addWahlkreisPermission($this->wahlkreisRepository->find($wahlkreis));
        }
        $user->setRoles([$userData->role]);
        $this->userRepository->save($user);
    }

    /**
     * @return array<
     *     array-key,
     *     array{
     *         'wahlkreis': string,
     *         'assigned': bool
     *     }
     * >
     */
    public function getPermissionsByUser(User $user): array
    {
        $data = [];

        foreach ($this->wahlkreisRepository->findAll() as $wahlkreis) {
            $data[] = [
                'wahlkreis' => $wahlkreis,
                'assigned' => $user->getWahlkreisPermission()->contains($wahlkreis),
            ];
        }

        return $data;
    }
}
