<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Integration\Repository;

use App\Dto\WahlkreisType;
use App\Entity\SupportNumbersDirektkandidat;
use App\Entity\SupportNumbersLandesliste;
use App\Repository\SupportNumbersRepository;
use App\Repository\UserRepository;
use App\Repository\WahlkreisRepository;
use App\Tests\Builder\Entity\SupportNumbersBuilder;
use App\Tests\Builder\Entity\UserBuilder;
use App\Tests\Builder\Entity\WahlkreisBuilder;
use App\Tests\Helper\FakerTrait;
use App\Tests\Helper\RepositoryTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Ulid;

class SupportNumbersRepositoryTest extends KernelTestCase
{
    use FakerTrait;
    use RepositoryTestCase;

    private const string REPO_CLASS = SupportNumbersRepository::class;
    private SupportNumbersRepository $repo;

    public function testSave(): void
    {
        $this->assertEmpty($this->repo->findAll());

        $user = UserBuilder::build();
        static::getContainer()->get(UserRepository::class)->save($user);

        $this->repo->save(SupportNumbersBuilder::build(
            data: [
                'user' => $user,
                'name' => 'Name A',
                'bundesland' => 'Land A',
                'approved' => 10,
                'updatedAt' => new \DateTimeImmutable(),
            ]
        ));
        $this->assertCount(1, $this->repo->findAll());
    }

    public function testDelete(): void
    {
        $user = UserBuilder::build();
        static::getContainer()->get(UserRepository::class)->save($user);

        $supportNumbers = SupportNumbersBuilder::build(
            data: [
                'user' => $user,
                'name' => 'Name A',
                'bundesland' => 'Land A',
                'approved' => 10,
                'updatedAt' => new \DateTimeImmutable(),
            ]
        );
        $this->repo->save($supportNumbers);
        $this->assertCount(1, $this->repo->findAll());

        $this->repo->delete($supportNumbers);
        $this->assertEmpty($this->repo->findAll());
    }

    public function testFindByState(): void
    {
        $user = UserBuilder::build();
        static::getContainer()->get(UserRepository::class)->save($user);

        $this->repo->save(SupportNumbersBuilder::build(
            data: [
                'user' => $user,
                'name' => 'Name A',
                'bundesland' => 'Land A',
                'approved' => 10,
                'updatedAt' => new \DateTimeImmutable(),
            ]
        ));
        $supportNumbers = SupportNumbersBuilder::build();
        $supportNumbers->setCreatedBy(UserBuilder::build());
        static::getContainer()->get(UserRepository::class)->save($supportNumbers->getCreatedBy());
        $this->repo->save($supportNumbers);

        $this->assertCount(1, $this->repo->findByState('Land A'));
    }

    #[DataProvider('provideSupportNumbers')]
    /**
     * @param SupportNumbersLandesliste[]|SupportNumbersDirektkandidat[] $supportNumbers
     */
    public function testFindAllBundeslaender(int $expected, array $supportNumbers): void
    {
        foreach ($supportNumbers as $supportNumber) {
            if ($supportNumber instanceof SupportNumbersDirektkandidat) {
                static::getContainer()->get(WahlkreisRepository::class)->save($supportNumber->getWahlkreis());
            }
            static::getContainer()->get(UserRepository::class)->save($supportNumber->getCreatedBy());
            $this->repo->save($supportNumber);
        }
        $this->assertCount($expected, $this->repo->findAllBundeslaender());
    }

    public static function provideSupportNumbers(): \Generator
    {
        yield [0, []];

        $wahlkreis = WahlkreisBuilder::build();
        $wahlkreis->setState(self::createFaker()->bundesland())
            ->setType(WahlkreisType::BTW)
            ->setYear(2025);
        $reflectionClass = new \ReflectionClass($wahlkreis);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($wahlkreis, new Ulid());

        yield [1, [
            SupportNumbersBuilder::build(data: ['bundesland' => 'Land A']),
            SupportNumbersBuilder::build(SupportNumbersDirektkandidat::TYPE, data: ['bundesland' => 'Land A', 'wahlkreis' => $wahlkreis, 'user' => UserBuilder::build()]),
        ]];
    }

    public function testGetNumbersByName(): void
    {
        $user = UserBuilder::buildWithId();
        self::getContainer()->get(UserRepository::class)->save($user);
        $this->repo->save(SupportNumbersBuilder::build(
            data: [
                'name' => 'Number name',
                'approved' => 10,
                'unapproved' => 5,
                'user' => $user,
            ]
        ));
        $this->repo->save(SupportNumbersBuilder::build(
            data: [
                'name' => 'Number name',
                'approved' => 10,
                'unapproved' => 0,
                'user' => $user,
            ]
        ));
        $result = $this->repo->getNumbersByName('Number name');
        $this->assertEquals(20, $result['approved']);
        $this->assertEquals(5, $result['unapproved']);
    }

    public function testRefresh(): void
    {
        $user = UserBuilder::buildWithId();
        self::getContainer()->get(UserRepository::class)->save($user);
        $supportNumbers = SupportNumbersBuilder::build(
            data: [
                'name' => 'Number name',
                'approved' => 10,
                'unapproved' => 0,
                'user' => $user,
            ]
        );
        $this->repo->save($supportNumbers);

        $this->repo->refresh($supportNumbers);
        $this->expectNotToPerformAssertions();
    }
}
