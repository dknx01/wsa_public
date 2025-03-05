<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Unit\Document;

use App\Document\DocumentType;
use App\Document\Handler;
use App\Document\UnsupportedDocumentTypeException;
use App\Dto\Api\FileUpload;
use App\Dto\Api\FileUploadDirektkandidat;
use App\Entity\Document;
use App\Entity\DocumentDirektkandidat;
use App\Entity\DocumentLandesliste;
use App\Entity\Wahlkreis;
use App\Repository\DocumentsRepository;
use App\Repository\WahlkreisRepository;
use App\Tests\Helper\FakerTrait;
use Faker\Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Constraint\IsInstanceOf;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Rule\InvokedCount as InvokedCountMatcher;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Ulid;

class HandlerTest extends TestCase
{
    use FakerTrait;
    use ProphecyTrait;

    protected function setUp(): void
    {
        $this->loadFaker();
    }

    public static function provideEdit(): \Generator
    {
        yield [
            new DocumentLandesliste(),
            null,
        ];

        yield [
            (new DocumentDirektkandidat())->setId(Uuid::uuid4()),
            new class extends UploadedFile {
                public function __construct()
                {
                    parent::__construct(
                        realpath(\dirname(__DIR__, 2).'/Faker/uploadExample.txt'), 'test.txt');
                }

                public function getClientOriginalName(): string
                {
                    return 'test.txt';
                }

                public function move(string $directory, ?string $name = null): File
                {
                    // do nothing
                    return $this;
                }
            },
        ];
    }

    #[DataProvider('provideEdit')]
    public function testHandleEdit(Document $document, ?UploadedFile $uploadedFile): void
    {
        $docRepo = $this->createMock(DocumentsRepository::class);
        $docRepo->expects($this->once())->method('refresh')->with(
            new IsInstanceOf(Document::class)
        );
        $docRepo->expects($this->once())->method('save')->with(
            new IsInstanceOf(Document::class)
        );
        $wahlkreisRepo = $this->createMock(WahlkreisRepository::class);
        $handler = new Handler(
            \dirname(\dirname(__DIR__, 2).\DIRECTORY_SEPARATOR.'Faker/var/'),
            $docRepo,
            $wahlkreisRepo,
        );

        $handler->handleEdit($document, $uploadedFile);
    }

    public static function provideUpload(): \Generator
    {
        yield [
            DocumentType::LANDESLISTE,
            fn (Generator $faker) => new FileUpload(
                state: $faker->bundesland(),
                file: new class extends UploadedFile {
                    public function __construct()
                    {
                        parent::__construct(
                            realpath(\dirname(__DIR__, 2).'/Faker/uploadExample.txt'), 'test.txt');
                    }

                    public function getClientOriginalName(): string
                    {
                        return 'test.txt';
                    }

                    public function move(string $directory, ?string $name = null): File
                    {
                        // do nothing
                        return $this;
                    }
                },
                description: $faker->realText(),
            ),
            static fn (MockObject $wahlkreisRepo) => null,
            DocumentLandesliste::class,
        ];

        yield [
            DocumentType::DIREKTKANDIDAT_BTW,
            fn (Generator $faker) => new FileUploadDirektkandidat(
                state: $faker->bundesland(),
                area: Ulid::generate(),
                file: new class extends UploadedFile {
                    public function __construct()
                    {
                        parent::__construct(
                            realpath(\dirname(__DIR__, 2).'/Faker/uploadExample.txt'), 'test.txt');
                    }

                    public function getClientOriginalName(): string
                    {
                        return 'test.txt';
                    }

                    public function move(string $directory, ?string $name = null): File
                    {
                        // do nothing
                        return $this;
                    }
                },
                description: $faker->realText(),
            ),
            static fn (MockObject $wahlkreisRepo) => $wahlkreisRepo->expects(new InvokedCountMatcher(1))
                    ->method('find')
                    ->with(new IsInstanceOf(Ulid::class))
                    ->willReturn(new Wahlkreis()),
            DocumentDirektkandidat::class,
        ];
    }

    #[DataProvider('provideUpload')]
    public function testHandleUpload(DocumentType|string $type, callable $fileUploadCallback, callable $wahlkreisCallback, string $saveClass): void
    {
        $docRepo = $this->createMock(DocumentsRepository::class);
        $docRepo->expects($this->once())->method('save')->with(
            new IsInstanceOf($saveClass)
        );
        $wahlkreisRepo = $this->createMock(WahlkreisRepository::class);
        $wahlkreisCallback($wahlkreisRepo);
        $handler = new Handler(
            \dirname(\dirname(__DIR__, 2).\DIRECTORY_SEPARATOR.'Faker/var/'),
            $docRepo,
            $wahlkreisRepo,
        );
        $handler->handleUpload($fileUploadCallback($this->faker), $type);
    }

    public function testHandleUploadWithException(): void
    {
        $this->expectException(UnsupportedDocumentTypeException::class);
        $docRepo = $this->createMock(DocumentsRepository::class);
        $docRepo->expects($this->never())->method('save');
        $wahlkreisRepo = $this->createMock(WahlkreisRepository::class);
        $handler = new Handler(
            \dirname(\dirname(__DIR__, 2).\DIRECTORY_SEPARATOR.'Faker/var/'),
            $docRepo,
            $wahlkreisRepo,
        );
        $handler->handleUpload(new FileUpload(
            state: $this->faker->bundesland(),
            file: new class extends UploadedFile {
                public function __construct()
                {
                    parent::__construct(
                        realpath(\dirname(__DIR__, 2).'/Faker/uploadExample.txt'), 'test.txt');
                }

                public function getClientOriginalName(): string
                {
                    return 'test.txt';
                }

                public function move(string $directory, ?string $name = null): File
                {
                    // do nothing
                    return $this;
                }
            },
            description: $this->faker->realText(),
        ), 'foo');
    }
}
