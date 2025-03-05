<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Unit\User;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\WahlkreisRepository;
use App\Tests\Builder\Entity\WahlkreisBuilder;
use App\Tests\Helper\FakerTrait;
use App\User\EditDvo;
use App\User\Roles;
use App\User\UserAdminService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UserAdminServiceTest extends TestCase
{
    use FakerTrait;

    protected function setUp(): void
    {
        $this->loadFaker();
    }

    public function testSaveUser(): void
    {
        /** @var MockObject|UserRepository $wahlkreisRepository */
        $userRepository = $this->createMock(UserRepository::class);
        /** @var MockObject|WahlkreisRepository $wahlkreisRepository */
        $wahlkreisRepository = $this->createMock(WahlkreisRepository::class);

        $service = new UserAdminService($wahlkreisRepository, $userRepository);

        $user = new User();

        $wahlkreis = WahlkreisBuilder::build();
        $userData = new EditDvo(
            $this->faker->safeEmail(),
            true,
            [$wahlkreis->getId()->toString() => 'active'],
            Roles::ROLE_SUPER_ADMIN->name
        );

        $wahlkreisRepository->expects($this->once())
            ->method('find')
            ->with($wahlkreis->getId()->toString())
            ->willReturn($wahlkreis);

        $userRepository->expects($this->once())
            ->method('save')
            ->with($user);
        $service->saveUser($user, $userData);

        $this->assertContains($userData->role, $user->getRoles());
        $this->assertTrue($user->getWahlkreisPermission()->contains($wahlkreis));
    }

    public function testGetPermissionsByUser(): void
    {
        /** @var MockObject|UserRepository $wahlkreisRepository */
        $userRepository = $this->createMock(UserRepository::class);
        /** @var MockObject|WahlkreisRepository $wahlkreisRepository */
        $wahlkreisRepository = $this->createMock(WahlkreisRepository::class);

        $service = new UserAdminService($wahlkreisRepository, $userRepository);

        $user = new User();
        $wahlkreis = WahlkreisBuilder::build();
        $wahlkreis2 = WahlkreisBuilder::build();
        $user->getWahlkreisPermission()->add($wahlkreis);

        $wahlkreisRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([$wahlkreis, $wahlkreis2]);

        $permissions = $service->getPermissionsByUser($user);

        $this->assertCount(2, $permissions);
        $this->assertTrue($permissions[0]['assigned']);
        $this->assertFalse($permissions[1]['assigned']);
    }
}
