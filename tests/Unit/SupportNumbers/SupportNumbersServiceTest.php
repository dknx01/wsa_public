<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Unit\SupportNumbers;

use App\Dto\Statistic;
use App\Entity\SupportNumbersLandesliste;
use App\Entity\User;
use App\Repository\SupportNumbersRepository;
use App\SupportNumbers\Output;
use App\SupportNumbers\SupportNumbersService;
use App\SupportNumbers\Type;
use PHPUnit\Framework\Constraint\IsInstanceOf;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Uid\Ulid;

class SupportNumbersServiceTest extends TestCase
{
    use ProphecyTrait;

    private Output|ObjectProphecy $output;
    private SupportNumbersRepository|MockObject $repository;
    private ArrayAdapter $cache;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(SupportNumbersRepository::class);
        $this->output = $this->prophesize(Output::class);
        $this->cache = new ArrayAdapter();
    }

    public function testGetByState(): void
    {
        $this->output->getDataByState('Berlin')->shouldBeCalled()->willReturn([
            new Statistic(
                'Berlin',
                10,
                1,
                20,
                30,
                2,
                'red',
                'red',
                2000,
                'failed',
                Type::DK_BTW->value
            ),
        ]);
        $this->assertCount(1, $this->getService()->getByState('Berlin'));
    }

    public function testDelete(): void
    {
        $supportNumber = new SupportNumbersLandesliste();
        $supportNumber->setId(new Ulid())
            ->setCreatedBy(new User());
        $supportNumber->setApproved(10)
            ->setUnapproved(0)
            ->setName('Berlin');

        $this->cache->get('Berlin', fn (CacheItem $item) => $supportNumber);

        $this->repository->expects($this->once())->method('save')->with(new IsInstanceOf(SupportNumbersLandesliste::class));
        $this->repository->expects($this->once())->method('getNumbersByName')->with('Berlin')
            ->willReturn(['approved' => '0', 'unapproved' => '0']);

        $this->getService()->delete($supportNumber);

        $this->assertInstanceOf(\DateTimeImmutable::class, $supportNumber->getDeletedAt());
        $this->assertInstanceOf(User::class, $supportNumber->getUpdatedBy());
        $this->assertTrue($supportNumber->isDeleted());
        $this->assertCount(1, $this->cache->getValues());
    }

    public function testSave(): void
    {
        $this->repository->expects($this->once())->method('save')->with(new IsInstanceOf(SupportNumbersLandesliste::class));
        $this->repository->expects($this->once())->method('refresh')->with(new IsInstanceOf(SupportNumbersLandesliste::class));
        $this->repository->expects($this->once())->method('getNumbersByName')
            ->willReturn([
                'approved' => 1, 'unapproved' => 1,
            ]);

        $supportNumbersService = $this->getService();

        $user = new User();
        $supportNumber = new SupportNumbersLandesliste();
        $supportNumber->setName('Landesliste Foo');

        $supportNumbersService->save($supportNumber, $user);

        $this->assertCount(1, $this->cache->getValues());
    }

    public function testGetByName(): void
    {
        $supportNumber = new SupportNumbersLandesliste();
        $supportNumber->setId(new Ulid())
            ->setCreatedBy(new User())
            ->setApproved(10)
            ->setUnapproved(0)
            ->setName('Berlin');
        $this->repository->expects($this->once())->method('getNumbersByName')->with($supportNumber->getName())
            ->willReturn(['approved' => $supportNumber->getApproved(), 'unapproved' => $supportNumber->getUnapproved()]);
        $result = $this->getService()->getByName($supportNumber);
        $this->assertEquals($supportNumber->getApproved(), $result->approved);
        $this->assertEquals($supportNumber->getUnapproved(), $result->unapproved);
    }

    private function getService(): SupportNumbersService
    {
        $slugger = new AsciiSlugger();

        return new SupportNumbersService(
            $this->repository,
            $this->cache,
            $slugger,
            $this->output->reveal()
        );
    }
}
