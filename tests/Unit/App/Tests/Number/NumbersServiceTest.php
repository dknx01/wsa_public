<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Number;

use App\Dto\Statistic;
use App\Entity\User;
use App\Entity\Wahlkreis;
use App\Entity\WahlkreisNumber;
use App\Number\NumberDto;
use App\Number\NumbersItem;
use App\Number\NumbersService;
use App\Number\Output;
use App\Repository\WahlkreisNumberRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\String\Slugger\AsciiSlugger;

class NumbersServiceTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy|WahlkreisNumberRepository $numbersRepo;
    private ObjectProphecy|Output $outputService;
    private ArrayAdapter $cache;

    protected function setUp(): void
    {
        $this->cache = new ArrayAdapter();
        $this->numbersRepo = $this->prophesize(WahlkreisNumberRepository::class);
        $this->outputService = $this->prophesize(Output::class);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testSaveEntity(): void
    {
        $user = new User();
        $wahlkreisNumber = new WahlkreisNumber();
        $wahlkreisNumber->setName('test');
        $wahlkreisNumber->setWahlkreis(new Wahlkreis());
        $wahlkreisNumber->setApproved(5);
        $wahlkreisNumber->setUpdatedAt(new \DateTimeImmutable());
        $wahlkreisNumber->setComment('Excuse my language but I have had it with you ruffling my petticoats!');

        $this->numbersRepo->save(Argument::that(
            fn (WahlkreisNumber $wahlkreisNumber): bool => $this->assertEntityValuesUpdated($wahlkreisNumber, $user))
        )
            ->shouldBeCalledOnce();
        $this->numbersRepo->refresh(Argument::any())
            ->shouldNotBeCalled();
        $this->numbersRepo->getNumbersByName('test')
            ->shouldBeCalled()
            ->willReturn(['approved' => 5, 'unapproved' => 0]);

        $this->getService()->saveEntity($wahlkreisNumber, $user);
        $this->assertNotEmpty($this->cache->getValues());
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testGetByName(): void
    {
        $this->numbersRepo->getNumbersByName('test')
            ->shouldBeCalled()
            ->willReturn(['approved' => 5, 'unapproved' => 0]);

        $number = new WahlkreisNumber();
        $number->setName('test');
        $actual = $this->getService()->getByName($number);
        $this->assertEquals(5, $actual->approved);
        $this->assertEquals(0, $actual->unapproved);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testGetByNameFromCache(): void
    {
        $this->numbersRepo->getNumbersByName('test')
            ->shouldNotBeCalled();
        $this->cache->get('test', fn (CacheItem $item) => new NumbersItem(5, 0));

        $number = new WahlkreisNumber();
        $number->setName('test');
        $actual = $this->getService()->getByName($number);
        $this->assertEquals(5, $actual->approved);
        $this->assertEquals(0, $actual->unapproved);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testGetByState(): void
    {
        $this->outputService->getDataByState('test')
            ->shouldBeCalledOnce()
            ->willReturn([new Statistic(
                'test Name',
                5,
                10,
                0,
                50,
                0,
                'yellow',
                'yellow',
                100,
                'not finished',
                'AGH'
            )]);
        $this->getService()->getByState('test');
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testSave(): void
    {
        $user = new User();
        $dto = new NumberDto();
        $dto->name = 'test';
        $dto->wahlkreis = new Wahlkreis();
        $dto->approved = 5;
        $dto->updatedAt = new \DateTime();
        $dto->comment = 'Excuse my language but I have had it with you ruffling my petticoats!';

        $this->numbersRepo->save(Argument::that(
            fn (WahlkreisNumber $wahlkreisNumber): bool => $this->assertEntityValues($wahlkreisNumber, $user, $dto))
        )
            ->shouldBeCalledOnce();
        $this->numbersRepo->refresh(Argument::type(WahlkreisNumber::class))
            ->shouldBeCalledOnce();
        $this->numbersRepo->getNumbersByName('test')
            ->shouldBeCalled()
            ->willReturn(['approved' => 5, 'unapproved' => 0]);

        $this->getService()->save($dto, $user);

        $this->assertNotEmpty($this->cache->getValues());
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testDelete(): void
    {
        /** @var NumbersItem $cached */
        $cached = $this->cache->get('test', fn (CacheItem $item) => new NumbersItem(10, 0));

        $this->assertEquals(10, $cached->approved);

        $user = new User();
        $wahlkreisNumber = new WahlkreisNumber();
        $wahlkreisNumber->setName('test');
        $wahlkreisNumber->setWahlkreis(new Wahlkreis());
        $wahlkreisNumber->setApproved(5);
        $wahlkreisNumber->setUpdatedAt(new \DateTimeImmutable());
        $wahlkreisNumber->setCreatedBy($user);
        $wahlkreisNumber->setComment('Excuse my language but I have had it with you ruffling my petticoats!');

        $this->numbersRepo->save(Argument::that(
            fn (WahlkreisNumber $wahlkreisNumber): bool => $this->assertEntityValuesSaved($wahlkreisNumber))
        )
            ->shouldBeCalledOnce();
        $this->numbersRepo->refresh(Argument::any())
            ->shouldNotBeCalled();
        $this->numbersRepo->getNumbersByName('test')
            ->shouldBeCalled()
            ->willReturn(['approved' => 5, 'unapproved' => 0]);

        $this->getService()->delete($wahlkreisNumber);
        /** @var NumbersItem $cached */
        $cached = $this->cache->get('test', fn (CacheItem $item) => new NumbersItem(0, 0));

        $this->assertEquals(5, $cached->approved);
    }

    private function assertEntityValues(WahlkreisNumber $number, User $user, NumberDto $dto): true
    {
        $this->assertSame($user, $number->getCreatedBy());
        $this->assertSame($user, $number->getUpdatedBy());
        $this->assertEquals($dto->approved, $number->getApproved());
        $this->assertEquals($dto->unapproved, $number->getUnapproved());

        return true;
    }

    private function assertEntityValuesUpdated(WahlkreisNumber $number, User $user): true
    {
        $this->assertSame($user, $number->getUpdatedBy());
        $this->assertNotNull($number->getUpdatedAt());

        return true;
    }

    private function getService(): NumbersService
    {
        return new NumbersService(
            wahlkreisNumberRepository: $this->numbersRepo->reveal(),
            numbersCache: $this->cache,
            slugger: new AsciiSlugger(),
            output: $this->outputService->reveal()
        );
    }

    private function assertEntityValuesSaved(WahlkreisNumber $wahlkreisNumber): true
    {
        $this->assertTrue($wahlkreisNumber->isDeleted());
        $this->assertNotNull($wahlkreisNumber->getDeletedAt());

        return true;
    }
}
