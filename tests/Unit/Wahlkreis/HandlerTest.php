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
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class HandlerTest extends TestCase
{
    use ProphecyTrait;
    private WahlkreisRepository|MockObject $wahlkreisRepo;
    private ObjectProphecy|AuthorizationCheckerInterface $authChecker;

    protected function setUp(): void
    {
        $this->authChecker = $this->prophesize(AuthorizationCheckerInterface::class);
        $this->wahlkreisRepo = $this->createMock(WahlkreisRepository::class);
    }

    public function testGetStatesWithoutUser(): void
    {
        $this->authChecker->isGranted(Argument::any(), Argument::any())->shouldNotBeCalled();

        $this->wahlkreisRepo->expects($this->once())
            ->method('getStates')
            ->willReturn(['Hessen' => 'Hessen', 'Berlin' => 'Berlin']);

        $handler = $this->getHandler();
        $this->assertEquals(['Berlin' => 'Berlin', 'Hessen' => 'Hessen'], $handler->getStates());
    }

    public function testGetStatesWithUserNonAdmin(): void
    {
        $this->authChecker->isGranted(Roles::ROLE_ADMIN->name, Argument::type(User::class))
            ->shouldBeCalledOnce()
            ->willReturn(false);

        $this->wahlkreisRepo->expects($this->once())
            ->method('getStates')
            ->willReturn(['Hessen' => 'Hessen', 'Berlin' => 'Berlin']);

        $handler = $this->getHandler();
        $this->assertEquals(['Berlin' => 'Berlin', 'Hessen' => 'Hessen'], $handler->getStates(UserBuilder::build()));
    }

    public function testGetStatesWithUserAdmin(): void
    {
        $this->authChecker->isGranted(Roles::ROLE_ADMIN->name, Argument::type(User::class))
            ->shouldBeCalledOnce()
            ->willReturn(false);

        $this->wahlkreisRepo->expects($this->never())
            ->method('getStates');

        $handler = $this->getHandler();
        $user = UserBuilder::build();
        $user->setRoles([Roles::ROLE_USER->name]);
        $wahlkreis = WahlkreisBuilder::build();
        $user->getWahlkreisPermission()->add($wahlkreis);
        $this->assertEquals([$wahlkreis->getState() => $wahlkreis->getState()], $handler->getStates($user));
    }

    public function testGetWahlkreiseByStateFormattedWithoutUser(): void
    {
        $wahlkreis2 = WahlkreisBuilder::build();
        $wahlkreis2->setState('Berlin');
        $wahlkreis1 = WahlkreisBuilder::build();
        $wahlkreis1->setState('Bayern');
        $wahlkreis1->setNumber(null);

        $this->wahlkreisRepo->expects($this->once())
            ->method('findBy')
            ->with(['state' => $wahlkreis2->getState()])
            ->willReturn([$wahlkreis2, $wahlkreis1]);
        $service = $this->getHandler();

        $result = $service->getWahlkreiseByStateFormatted('Berlin');
        $this->assertCount(2, $result);
        $this->assertArrayHasKey($wahlkreis2->getId()->toString(), $result);
        $this->assertEquals(\sprintf('%s (Nr. %s)', $wahlkreis2->getName(), $wahlkreis2->getNumber()), $result[$wahlkreis2->getId()->toString()]);

        $this->assertArrayHasKey($wahlkreis1->getId()->toString(), $result);
        $this->assertEquals(\sprintf('%s', $wahlkreis1->getName()), $result[$wahlkreis1->getId()->toString()]);
    }

    public function testGetWahlkreiseByStateFormattedWithNonAdminUser(): void
    {
        $wahlkreis2 = WahlkreisBuilder::build();
        $wahlkreis2->setState('Berlin');
        $wahlkreis3 = WahlkreisBuilder::build();
        $wahlkreis3->setState('Berlin');
        $wahlkreis3->setNumber(null);
        $wahlkreis1 = WahlkreisBuilder::build();
        $wahlkreis1->setState('Bayern');

        $this->wahlkreisRepo->expects($this->never())
            ->method('findBy');
        $this->authChecker->isGranted(Roles::ROLE_ADMIN->name, Argument::type(User::class))
            ->shouldBeCalledOnce()
            ->willReturn(false);

        $service = $this->getHandler();
        $user = UserBuilder::build();
        $user->getWahlkreisPermission()->add($wahlkreis2);
        $user->getWahlkreisPermission()->add($wahlkreis3);
        $user->getWahlkreisPermission()->add($wahlkreis1);

        $result = $service->getWahlkreiseByStateFormatted('Berlin', $user);
        $this->assertCount(2, $result);
        $this->assertArrayHasKey($wahlkreis2->getId()->toString(), $result);
        $this->assertEquals(\sprintf('%s (Nr. %s)', $wahlkreis2->getName(), $wahlkreis2->getNumber()), $result[$wahlkreis2->getId()->toString()]);

        $this->assertArrayHasKey($wahlkreis3->getId()->toString(), $result);
        $this->assertEquals(\sprintf('%s', $wahlkreis3->getName()), $result[$wahlkreis3->getId()->toString()]);
    }

    public function testGetWahlkreiseFormattedWithoutUser(): void
    {
        $wahlkreis2 = WahlkreisBuilder::build();
        $wahlkreis1 = WahlkreisBuilder::build();
        $wahlkreis1->setNumber(null);

        $this->wahlkreisRepo->expects($this->once())
            ->method('findAll')
            ->willReturn([$wahlkreis1, $wahlkreis2]);
        $service = $this->getHandler();

        $expectedKeys = [
            \sprintf('%s (Nr. %s)', $wahlkreis2->getName(), $wahlkreis2->getNumber()) => $wahlkreis2->getId()->toString(),
            \sprintf('%s', $wahlkreis1->getName()) => $wahlkreis1->getId()->toString(),
        ];
        $wahlkreise = $service->getWahlkreiseFormatted();
        foreach ($expectedKeys as $key => $value) {
            $this->assertArrayHasKey($key, $wahlkreise);
            $this->assertEquals($value, $wahlkreise[$key]);
        }
    }

    public function testGetWahlkreiseFormattedWithNonAdminUser(): void
    {
        $wahlkreis2 = WahlkreisBuilder::build();
        $wahlkreis1 = WahlkreisBuilder::build();

        $user = UserBuilder::build();
        $user->getWahlkreisPermission()->add($wahlkreis1);
        $user->getWahlkreisPermission()->add($wahlkreis2);

        $this->wahlkreisRepo->expects($this->never())
            ->method('findAll');
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

    private function getHandler(): Handler
    {
        return new Handler(
            $this->wahlkreisRepo,
            $this->authChecker->reveal()
        );
    }
}
