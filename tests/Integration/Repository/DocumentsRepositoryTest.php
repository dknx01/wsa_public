<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Integration\Repository;

use App\Entity\DocumentDirektkandidat;
use App\Entity\DocumentLandesliste;
use App\Repository\DocumentsRepository;
use App\Tests\Helper\RepositoryTestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DocumentsRepositoryTest extends KernelTestCase
{
    use RepositoryTestCase;
    private const string REPO_CLASS = DocumentsRepository::class;
    private DocumentsRepository $repo;

    public function testSave(): void
    {
        $this->assertEmpty($this->repo->findAll());

        $document = new DocumentLandesliste();
        $document->setName('Example Document');
        $document->setState('Berlin');
        $document->setFileName('foo.pdf');
        $this->repo->save($document);

        $this->assertCount(1, $this->repo->findAll());
    }

    public function testRefresh(): void
    {
        $document = new DocumentLandesliste();
        $document->setName('Example Document');
        $document->setState('Berlin');
        $document->setFileName('foo.pdf');
        $this->repo->save($document);
        $this->repo->refresh($document);
        $this->expectNotToPerformAssertions();
    }

    public function testFindAllBundeslaender(): void
    {
        $document = new DocumentLandesliste();
        $document->setName('Example Document');
        $document->setState('Berlin');
        $document->setFileName('foo.pdf');
        $this->repo->save($document);

        $this->assertSame(['Berlin'], $this->repo->findAllBundeslaender());
    }

    public function testFindAllByState(): void
    {
        $document = new DocumentLandesliste();
        $document->setName('Example Document');
        $document->setState('Berlin');
        $document->setFileName('foo.pdf');
        $this->repo->save($document);

        $this->assertCount(1, $this->repo->findAllByState('Berlin'));
    }

    public function testDelete(): void
    {
        $document = new DocumentDirektkandidat();
        $document->setName('Example Document');
        $document->setState('Berlin');
        $document->setFileName('foo.pdf');
        $this->repo->save($document);
        $this->assertCount(1, $this->repo->findAll());

        $this->repo->delete($document);
        $this->assertEmpty($this->repo->findAll());
    }

    public function testFindByName(): void
    {
        $document = new DocumentDirektkandidat();
        $document->setName('Example Document');
        $document->setState('Berlin');
        $document->setFileName('foo.pdf');
        $this->repo->save($document);
        unset($document);

        $document = $this->repo->findByName('Example Document');
        $this->assertSame('Example Document', $document[0]->getName());
    }
}
