<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Unit\Statistic;

use App\Entity\Statistic;
use App\Entity\SupportNumbersDirektkandidat;
use App\Repository\StatisticRepository;
use App\Repository\SupportNumbersRepository;
use App\Statistic\Handler;
use App\Statistic\OverviewItem;
use App\Statistic\StatisticDataItem;
use App\Statistic\StatisticOverviewCollection;
use App\Tests\Builder\Entity\SupportNumbersBuilder;
use App\Tests\Builder\Entity\UserBuilder;
use App\Tests\Builder\Entity\WahlkreisBuilder;
use App\User\Roles;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Ulid;

class HandlerTest extends TestCase
{
    use ProphecyTrait;
    private MockObject|StatisticRepository $statisticRepo;
    private SupportNumbersRepository|MockObject $supportNumbersRepo;
    private ObjectProphecy|AuthorizationCheckerInterface $authChecker;

    protected function setUp(): void
    {
        $this->statisticRepo = $this->createMock(StatisticRepository::class);
        $this->supportNumbersRepo = $this->createMock(SupportNumbersRepository::class);
        $this->authChecker = $this->prophesize(AuthorizationCheckerInterface::class);
    }

    public function testFindByStateWithoutUser(): void
    {
        $supportNumber1 = SupportNumbersBuilder::build();
        $supportNumber1->setCreatedAt(new \DateTimeImmutable());
        $supportNumber1->setUpdatedAt($supportNumber1->getCreatedAt())
            ->setCreatedBy(UserBuilder::buildWithId());

        $this->supportNumbersRepo->expects($this->once())
            ->method('findByState')
            ->with('Berlin')
            ->willReturn([$supportNumber1]);
        $handler = $this->getHandler();
        $statisticDataItem = new StatisticDataItem(
            null,
            $supportNumber1->getApproved(),
            $supportNumber1->getUnapproved(),
            $supportNumber1->getName(),
            $supportNumber1->getCreatedBy()->getUserIdentifier(),
            $supportNumber1->getCreatedAt()->format('Y-m-d H:i:s'),
            $supportNumber1->getUpdatedAt()->format('Y-m-d H:i:s'),
            $supportNumber1->getType(),
            $supportNumber1->getBundesland(),
            $supportNumber1->getComment() ?: ''
        );
        $this->assertEquals(
            [$statisticDataItem],
            $handler->findByState('Berlin', null)
        );
    }

    public function testFindByStateWithUserWithoutAccess(): void
    {
        $supportNumber1 = SupportNumbersBuilder::build();
        $supportNumber1->setId(new Ulid());
        $supportNumber1->setCreatedAt(new \DateTimeImmutable());
        $supportNumber1->setUpdatedAt($supportNumber1->getCreatedAt())
            ->setCreatedBy(UserBuilder::buildWithId());

        $this->supportNumbersRepo->expects($this->once())
            ->method('findByState')
            ->with('Berlin')
            ->willReturn([$supportNumber1]);
        $handler = $this->getHandler();
        $statisticDataItem = new StatisticDataItem(
            null,
            $supportNumber1->getApproved(),
            $supportNumber1->getUnapproved(),
            $supportNumber1->getName(),
            $supportNumber1->getCreatedBy()->getUserIdentifier(),
            $supportNumber1->getCreatedAt()->format('Y-m-d H:i:s'),
            $supportNumber1->getUpdatedAt()->format('Y-m-d H:i:s'),
            $supportNumber1->getType(),
            $supportNumber1->getBundesland(),
            $supportNumber1->getComment() ?: ''
        );
        $user = UserBuilder::buildWithId();

        $this->authChecker->isGranted(Roles::ROLE_ADMIN->name)
            ->shouldBeCalledOnce()->willReturn(false);

        $this->assertEquals(
            [$statisticDataItem],
            $handler->findByState('Berlin', $user)
        );
    }

    public function testFindByStateWithUserWithAccess(): void
    {
        $supportNumber1 = SupportNumbersBuilder::build();
        $supportNumber1->setId(new Ulid());
        $supportNumber1->setCreatedAt(new \DateTimeImmutable());
        $supportNumber1->setUpdatedAt($supportNumber1->getCreatedAt())
            ->setCreatedBy(UserBuilder::buildWithId());

        $this->supportNumbersRepo->expects($this->once())
            ->method('findByState')
            ->with('Berlin')
            ->willReturn([$supportNumber1]);
        $handler = $this->getHandler();
        $statisticDataItem = new StatisticDataItem(
            $supportNumber1->getId(),
            $supportNumber1->getApproved(),
            $supportNumber1->getUnapproved(),
            $supportNumber1->getName(),
            $supportNumber1->getCreatedBy()->getUserIdentifier(),
            $supportNumber1->getCreatedAt()->format('Y-m-d H:i:s'),
            $supportNumber1->getUpdatedAt()->format('Y-m-d H:i:s'),
            $supportNumber1->getType(),
            $supportNumber1->getBundesland(),
            $supportNumber1->getComment() ?: ''
        );
        $user = UserBuilder::buildWithId();

        $this->authChecker->isGranted(Roles::ROLE_ADMIN->name)
            ->shouldBeCalled()->willReturn(true);

        $this->assertEquals(
            [$statisticDataItem],
            $handler->findByState('Berlin', $user)
        );
    }

    public function testFindByStateWithUserWithoutPermission(): void
    {
        $supportNumber1 = SupportNumbersBuilder::build(SupportNumbersDirektkandidat::TYPE);
        $supportNumber1->setId(new Ulid());
        $supportNumber1->setCreatedAt(new \DateTimeImmutable());
        $supportNumber1->setUpdatedAt($supportNumber1->getCreatedAt())
            ->setCreatedBy(UserBuilder::buildWithId());

        $this->supportNumbersRepo->expects($this->once())
            ->method('findByState')
            ->with('Berlin')
            ->willReturn([$supportNumber1]);
        $handler = $this->getHandler();
        $statisticDataItem = new StatisticDataItem(
            null,
            $supportNumber1->getApproved(),
            $supportNumber1->getUnapproved(),
            $supportNumber1->getName(),
            $supportNumber1->getCreatedBy()->getUserIdentifier(),
            $supportNumber1->getCreatedAt()->format('Y-m-d H:i:s'),
            $supportNumber1->getUpdatedAt()->format('Y-m-d H:i:s'),
            $supportNumber1->getType(),
            $supportNumber1->getBundesland(),
            $supportNumber1->getComment() ?: ''
        );
        $user = UserBuilder::buildWithId();

        $this->authChecker->isGranted(Roles::ROLE_ADMIN->name)
            ->shouldBeCalled()->willReturn(false);

        $this->assertEquals(
            [$statisticDataItem],
            $handler->findByState('Berlin', $user)
        );
    }

    public function testFindAllWithoutUser(): void
    {
        $statistic = new Statistic();
        $this->statisticRepo->expects($this->once())
            ->method('findAll')
            ->willReturn([$statistic]);
        $expected = new StatisticOverviewCollection();
        $overviewItem = new OverviewItem(
            $statistic,
        );
        $expected->add($overviewItem);

        $result = $this->getHandler()->findAll(null);
        $this->assertEquals($expected, $result);
    }

    public function testFindAllWithUser(): void
    {
        $wahlkreis1 = WahlkreisBuilder::build();
        $wahlkreis1->setState('Berlin');
        $statistic = new Statistic();
        $statistic->setBundesland($wahlkreis1->getState());
        $statistic->setType('Landesliste');
        $statistic->setName($wahlkreis1->getName());

        $wahlkreis2 = WahlkreisBuilder::build();
        $wahlkreis2->setState('Hessen');
        $statistic2 = new Statistic();
        $statistic2->setBundesland($wahlkreis2->getState());
        $statistic2->setType('Direktkandidat');
        $statistic2->setName($wahlkreis2->getName());

        $this->statisticRepo->expects($this->once())
            ->method('findAll')
            ->willReturn([$statistic, $statistic2]);
        $expected = new StatisticOverviewCollection();
        $overviewItem = new OverviewItem(
            $statistic,
            true
        );
        $overviewItem2 = new OverviewItem(
            $statistic2,
            false
        );
        $expected->add($overviewItem);
        $expected->add($overviewItem2);

        $user = UserBuilder::buildWithId();
        $user->getWahlkreisPermission()->add($wahlkreis1);

        $result = $this->getHandler()->findAll($user);
        $this->assertEquals($expected, $result);
    }

    public function testFindAllWithUserInterfaceObject(): void
    {
        $wahlkreis1 = WahlkreisBuilder::build();
        $wahlkreis1->setState('Berlin');
        $statistic = new Statistic();
        $statistic->setBundesland($wahlkreis1->getState());
        $statistic->setType('Landesliste');
        $statistic->setName($wahlkreis1->getName());

        $wahlkreis2 = WahlkreisBuilder::build();
        $wahlkreis2->setState('Hessen');
        $statistic2 = new Statistic();
        $statistic2->setBundesland($wahlkreis2->getState());
        $statistic2->setType('Direktkandidat');
        $statistic2->setName($wahlkreis2->getName());

        $this->statisticRepo->expects($this->once())
            ->method('findAll')
            ->willReturn([$statistic, $statistic2]);
        $expected = new StatisticOverviewCollection();
        $overviewItem = new OverviewItem(
            $statistic,
            false
        );
        $overviewItem2 = new OverviewItem(
            $statistic2,
            false
        );
        $expected->add($overviewItem);
        $expected->add($overviewItem2);

        $user = $this->prophesize(UserInterface::class);

        $result = $this->getHandler()->findAll($user->reveal());
        $this->assertEquals($expected, $result);
    }

    private function getHandler(): Handler
    {
        return new Handler($this->statisticRepo, $this->supportNumbersRepo, $this->authChecker->reveal());
    }
}
