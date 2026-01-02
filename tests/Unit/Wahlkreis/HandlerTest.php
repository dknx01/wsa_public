<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Unit\Wahlkreis;

use App\Entity\User;
use App\Repository\WahlkreisRepository;
use App\Tests\Builder\Entity\UserBuilder;
use App\Tests\Builder\Entity\WahlkreisBuilder;
use App\User\Roles;
use App\Wahlkreis\Handler;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Ulid;

class HandlerTest extends TestCase
{
    use ProphecyTrait;
    private WahlkreisRepository|ObjectProphecy $wahlkreisRepo;
    private ObjectProphecy|AuthorizationCheckerInterface $authChecker;

    protected function setUp(): void
    {
        $this->authChecker = $this->prophesize(AuthorizationCheckerInterface::class);
        $this->wahlkreisRepo = $this->prophesize(WahlkreisRepository::class);
    }

    public function testGetStatesWithoutUser(): void
    {
        $this->authChecker->isGranted(Argument::any(), Argument::any())->shouldNotBeCalled();

        $this->wahlkreisRepo->getStates()->shouldBeCalledOnce()
            ->willReturn(['Hessen' => 'Hessen', 'Berlin' => 'Berlin']);

        $handler = $this->getHandler();
        $this->assertEquals(['Berlin' => 'Berlin', 'Hessen' => 'Hessen'], $handler->getStates());
    }

    public function testGetStatesWithUserNonAdmin(): void
    {
        $this->authChecker->isGranted(Roles::ROLE_ADMIN->name, Argument::type(User::class))
            ->shouldBeCalledOnce()
            ->willReturn(false);

        $this->wahlkreisRepo->getStates()->shouldBeCalledOnce()
            ->willReturn(['Hessen' => 'Hessen', 'Berlin' => 'Berlin']);

        $handler = $this->getHandler();
        $this->assertEquals(['Berlin' => 'Berlin', 'Hessen' => 'Hessen'], $handler->getStates(UserBuilder::build()));
    }

    public function testGetStatesWithUserAdmin(): void
    {
        $this->authChecker->isGranted(Roles::ROLE_ADMIN->name, Argument::type(User::class))
            ->shouldBeCalledOnce()
            ->willReturn(false);

        $this->wahlkreisRepo->getStates()->shouldNotBeCalled();

        $handler = $this->getHandler();
        $user = UserBuilder::build();
        $user->setRoles([Roles::ROLE_USER->name]);
        $wahlkreis = WahlkreisBuilder::build();
        $user->addWahlkreisPermission($wahlkreis);
        $this->assertEquals([$wahlkreis->getState() => $wahlkreis->getState()], $handler->getStates($user));
    }

    public function testGetWahlkreiseByStateFormattedWithoutUser(): void
    {
        $wahlkreis2 = WahlkreisBuilder::build();
        $wahlkreis2->setState('Berlin');
        $wahlkreis1 = WahlkreisBuilder::build();
        $wahlkreis1->setState('Bayern');

        $this->wahlkreisRepo->findBy(['state' => $wahlkreis2->getState()])
            ->shouldBeCalledOnce()
            ->willReturn([$wahlkreis2]);
        $service = $this->getHandler();
        $this->assertSame(
            [
                $wahlkreis2->getId()->toString() => \sprintf('%s (Nr. %s) - %s %d', $wahlkreis2->getName(), $wahlkreis2->getNumber(), $wahlkreis2->getWahlkreistyp()?->getName(), $wahlkreis2->getYear()),
            ],
            $service->getWahlkreiseByStateFormatted('Berlin')
        );
    }

    public function testGetWahlkreiseByStateFormattedWithNonAdminUser(): void
    {
        $wahlkreis2 = WahlkreisBuilder::build();
        $wahlkreis2->setState('Berlin');
        $wahlkreis1 = WahlkreisBuilder::build();
        $wahlkreis1->setState('Bayern');

        $this->wahlkreisRepo->findBy(Argument::any())->shouldNotBeCalled();
        $this->authChecker->isGranted(Roles::ROLE_ADMIN->name, Argument::type(User::class))
            ->shouldBeCalledOnce()
            ->willReturn(false);

        $service = $this->getHandler();
        $user = UserBuilder::build();
        $user->addWahlkreisPermission($wahlkreis2);
        $user->addWahlkreisPermission($wahlkreis1);

        $this->assertSame(
            [
                $wahlkreis2->getId()->toString() => \sprintf('%s (Nr. %s) - %s %d', $wahlkreis2->getName(), $wahlkreis2->getNumber(), $wahlkreis2->getWahlkreistyp(), $wahlkreis2->getYear()),
            ],
            $service->getWahlkreiseByStateFormatted('Berlin', $user)
        );
    }

    public function testGetWahlkreiseFormattedWithoutUser(): void
    {
        $wahlkreis2 = WahlkreisBuilder::build();
        $wahlkreis1 = WahlkreisBuilder::build();

        $this->wahlkreisRepo->findAll()->shouldBeCalledOnce()
            ->willReturn([$wahlkreis1, $wahlkreis2]);
        $service = $this->getHandler();
        $result = $service->getWahlkreiseFormatted();
        $this->assertArrayHasKey(
            \sprintf('%s (Nr. %s)', $wahlkreis2->getName(), $wahlkreis2->getNumber()),
            $result
        );
        $this->assertArrayHasKey(
            \sprintf('%s (Nr. %s)', $wahlkreis1->getName(), $wahlkreis1->getNumber()),
            $result
        );
    }

    public function testGetWahlkreiseFormattedWithNonAdminUser(): void
    {
        $wahlkreis2 = WahlkreisBuilder::build();
        $wahlkreis2->setId(Ulid::fromString(Ulid::generate()));
        $wahlkreis1 = WahlkreisBuilder::build();
        $wahlkreis1->setId(Ulid::fromString(Ulid::generate()));

        $user = UserBuilder::build();
        $user->addWahlkreisPermission($wahlkreis1);
        $user->addWahlkreisPermission($wahlkreis2);

        $this->wahlkreisRepo->findAll()->shouldNotBeCalled();
        $this->authChecker->isGranted(Roles::ROLE_ADMIN->name, Argument::type(User::class))
            ->shouldBeCalledOnce()
            ->willReturn(false);

        $service = $this->getHandler();
        $this->assertSame(
            [
                \sprintf('%s (Nr. %s)', $wahlkreis2->getName(), $wahlkreis2->getNumber()) => $wahlkreis2->getId()->toString(),
                \sprintf('%s (Nr. %s)', $wahlkreis1->getName(), $wahlkreis1->getNumber()) => $wahlkreis1->getId()->toString(),
            ],
            $service->getWahlkreiseFormatted($user)
        );
    }

    public static function provideFindAllByUser(): \Generator
    {
        $wahlkreis1 = WahlkreisBuilder::build();
        $wahlkreis2 = WahlkreisBuilder::build();
        yield 'without user' => [
            [$wahlkreis1, $wahlkreis2],
            function (AuthorizationCheckerInterface|ObjectProphecy $authChecker, WahlkreisRepository|ObjectProphecy $wahlkreisRepository) use ($wahlkreis1, $wahlkreis2) {
                $wahlkreisRepository->findAllOrdered()->shouldBeCalled()
                    ->willReturn([$wahlkreis1, $wahlkreis2]);
            },
            null,
        ];
        yield 'Admin user' => [
            [$wahlkreis1, $wahlkreis2],
            function (AuthorizationCheckerInterface|ObjectProphecy $authChecker, WahlkreisRepository|ObjectProphecy $wahlkreisRepository) use ($wahlkreis1, $wahlkreis2) {
                $wahlkreisRepository->findAllOrdered()->shouldBeCalled()
                    ->willReturn([$wahlkreis1, $wahlkreis2]);
                $authChecker->isGranted(Roles::ROLE_ADMIN->name, Argument::type(User::class))
                    ->shouldBeCalledOnce()
                    ->willReturn(true);
            },
            UserBuilder::build(),
        ];

        $user = UserBuilder::build();
        $user->addWahlkreisPermission($wahlkreis2);
        yield 'With normal user' => [
            [$wahlkreis2],
            function (AuthorizationCheckerInterface|ObjectProphecy $authChecker, WahlkreisRepository|ObjectProphecy $wahlkreisRepository) {
                $wahlkreisRepository->findAllOrdered()->shouldNotBeCalled();
                $authChecker->isGranted(Roles::ROLE_ADMIN->name, Argument::type(User::class))
                    ->shouldBeCalledOnce()
                    ->willReturn(false);
            },
            $user,
        ];
    }

    #[DataProvider('provideFindAllByUser')]
    public function testFindAllByUser(array $expected, callable $prophesizeCallback, UserInterface|User|null $user): void
    {
        $prophesizeCallback($this->authChecker, $this->wahlkreisRepo);

        $this->assertEquals(
            $expected,
            $this->getHandler()->findAllByUser($user)
        );
    }

    private function getHandler(): Handler
    {
        return new Handler(
            $this->wahlkreisRepo->reveal(),
            $this->authChecker->reveal()
        );
    }
}
