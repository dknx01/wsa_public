<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Integration\Repository;

use App\Entity\Wahlkreistyp;
use App\Repository\WahlkreistypRepository;
use App\Tests\Helper\RepositoryTestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class WahlkreistypRepositoryTest extends KernelTestCase
{
    use RepositoryTestCase;

    private const string REPO_CLASS = WahlkreistypRepository::class;
    private WahlkreistypRepository $repo;

    public function testSave(): void
    {
        $this->assertEmpty($this->repo->findAll());
        $wahlkreistyp = new Wahlkreistyp();
        $wahlkreistyp->setName('Foo')
            ->setAbbreviation('F');
        $this->repo->save($wahlkreistyp);

        $this->assertCount(1, $this->repo->findAll());
    }

    public function testDeleteByName(): void
    {
        $wahlkreistyp = new Wahlkreistyp();
        $wahlkreistyp->setName('Foo')
            ->setAbbreviation('F');
        $this->repo->save($wahlkreistyp);

        $this->assertCount(1, $this->repo->findAll());
        $this->repo->deleteByName($wahlkreistyp->getName());
        $this->assertCount(0, $this->repo->findAll());
    }

    public function testFindAllOrdered(): void
    {
        $wahlkreistyp = new Wahlkreistyp();
        $wahlkreistyp->setName('Foo')
            ->setAbbreviation('F');
        $this->repo->save($wahlkreistyp);
        $wahlkreistyp2 = new Wahlkreistyp();
        $wahlkreistyp2->setName('Bla');
        $this->repo->save($wahlkreistyp2);

        $this->assertEquals(
            [$wahlkreistyp2, $wahlkreistyp],
            $this->repo->findAllOrdered()
        );
    }
}
