<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Integration\Repository;

use App\Entity\Verbandsseiten;
use App\Repository\VerbandsseitenRepository;
use App\Tests\Helper\RepositoryTestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class VerbandsseitenRepositoryTest extends KernelTestCase
{
    use RepositoryTestCase;

    private const string REPO_CLASS = VerbandsseitenRepository::class;
    private VerbandsseitenRepository $repo;

    public function testSave(): void
    {
        $this->assertEmpty($this->repo->findAll());
        $seite = new Verbandsseiten();
        $seite->setName('Foo')
            ->setLink('http://foo.bar')
            ->setLinkName('Verbandsseite');
        $this->repo->save($seite);

        $this->assertCount(1, $this->repo->findAll());
    }
}
