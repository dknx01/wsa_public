<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Integration\Repository;

use App\Entity\Wahlkreis;
use App\Repository\WahlkreisRepository;
use App\Tests\Helper\RepositoryTestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

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
            ->setNumber(1);
        $this->repo->save($wahlkreis);

        $this->assertCount(1, $this->repo->findAll());
    }

    public function testGetStates(): void
    {
        $wahlkreis = new Wahlkreis();
        $wahlkreis->setName('Foo')
            ->setState('Bar')
            ->setNumber(1);
        $this->repo->save($wahlkreis);
        $wahlkreis2 = new Wahlkreis();
        $wahlkreis2->setName('FooFoo')
            ->setState('Bar')
            ->setNumber(1);
        $this->repo->save($wahlkreis2);

        $this->assertCount(1, $this->repo->getStates());
    }

    public function testGetWahlkreiseByStateFormatted(): void
    {
        $wahlkreis = new Wahlkreis();
        $wahlkreis->setName('Foo')
            ->setState('Bar')
            ->setNumber(1);
        $this->repo->save($wahlkreis);
        $wahlkreis2 = new Wahlkreis();
        $wahlkreis2->setName('FooFoo')
            ->setState('Bar')
            ->setNumber(2);
        $this->repo->save($wahlkreis2);

        $wahlkreise = $this->repo->getWahlkreiseByStateFormatted('Bar');
        $this->assertContains('Foo (Nr. 1)', $wahlkreise);
        $this->assertContains('FooFoo (Nr. 2)', $wahlkreise);
    }

    public function testGetWahlkreiseFormatted(): void
    {
        $wahlkreis = new Wahlkreis();
        $wahlkreis->setName('Foo')
            ->setState('Bar')
            ->setNumber(1);
        $this->repo->save($wahlkreis);
        $wahlkreis2 = new Wahlkreis();
        $wahlkreis2->setName('FooFoo')
            ->setState('BarBar')
            ->setNumber(2);
        $this->repo->save($wahlkreis2);

        $wahlkreise = $this->repo->getWahlkreiseFormatted();
        $this->assertArrayHasKey('Foo (Nr. 1)', $wahlkreise);
        $this->assertArrayHasKey('FooFoo (Nr. 2)', $wahlkreise);
    }
}
