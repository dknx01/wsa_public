<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Integration\Repository;

use App\Dto\WahlkreisType;
use App\Entity\User;
use App\Entity\Wahlkreis;
use App\Repository\WahlkreisRepository;
use App\Tests\Builder\Entity\WahlkreisBuilder;
use App\Tests\Helper\RepositoryTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\User\UserInterface;

class WahlkreisRepositoryTest extends KernelTestCase
{
    use RepositoryTestCase;

    private const string REPO_CLASS = WahlkreisRepository::class;
    private WahlkreisRepository $repo;

    public function testSave(): void
    {
        $this->assertEmpty($this->repo->findAll());
        $wahlkreis = new Wahlkreis();
        $wahlkreis->setName('Foo')
            ->setState('Bar')
            ->setNumber(1)
            ->setType(WahlkreisType::BTW)
            ->setYear(2025);
        $this->repo->save($wahlkreis);

        $this->assertCount(1, $this->repo->findAll());
    }

    public function testGetStates(): void
    {
        $wahlkreis = new Wahlkreis();
        $wahlkreis->setName('Foo')
            ->setState('Bar')
            ->setNumber(1)
            ->setType(WahlkreisType::BTW)
            ->setYear(2025);
        $this->repo->save($wahlkreis);
        $wahlkreis2 = new Wahlkreis();
        $wahlkreis2->setName('FooFoo')
            ->setState('Bar')
            ->setNumber(1)
            ->setType(WahlkreisType::BTW)
            ->setYear(2025);
        $this->repo->save($wahlkreis2);

        $this->assertCount(1, $this->repo->getStates());
    }

    #[DataProvider('provideStateUserData')]
    public function testGetStatesByUser(Wahlkreis $wahlkreis, User|UserInterface $user): void
    {
        $expected = [];
        if ($user instanceof User) {
            $user->getWahlkreisPermission()->add($wahlkreis);
        } else {
            $this->repo->save($wahlkreis);
        }
        $expected[$wahlkreis->getState()] = $wahlkreis->getState();
        $this->assertEquals($expected, $this->repo->getStatesByUser($user));
    }

    public static function provideStateUserData(): \Generator
    {
        yield [
            WahlkreisBuilder::build(),
            new class implements UserInterface {
                public function getRoles(): array
                {
                    return [];
                }

                public function eraseCredentials(): void
                {
                    // TODO: Implement eraseCredentials() method.
                }

                public function getUserIdentifier(): string
                {
                    return '';
                }
            },
        ];
        yield [
            WahlkreisBuilder::build(),
            new User(),
        ];
    }
}
