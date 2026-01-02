<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Unit\Document;

use App\Document\Handler;
use App\Dto\Document\FileDto;
use App\Dto\Document\WahlkreisDto;
use App\Entity\Document;
use App\Entity\Wahlkreis;
use App\Repository\DocumentsRepository;
use App\Repository\WahlkreisRepository;
use App\Tests\Helper\FakerTrait;
use Faker\Generator;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
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
            new Document(),
            null,
        ];

        yield [
            (new Document())->setId(Uuid::uuid4()),
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
        $docRepo = $this->prophesize(DocumentsRepository::class);
        $docRepo->refresh(Argument::type(Document::class))->shouldBeCalledOnce();
        $docRepo->save(Argument::type(Document::class))->shouldBeCalledOnce();
        $wahlkreisRepo = $this->prophesize(WahlkreisRepository::class);
        $handler = new Handler(
            \dirname(\dirname(__DIR__, 2).\DIRECTORY_SEPARATOR.'Faker/var/'),
            $docRepo->reveal(),
            $wahlkreisRepo->reveal(),
        );

        $handler->handleEdit($document, $uploadedFile);
    }

    public function testDelete(): void
    {
        $document = new Document();
        $document->setId(Uuid::uuid4());
        $docRepo = $this->prophesize(DocumentsRepository::class);
        $docRepo->delete($document)->shouldBeCalled();

        $root = vfsStream::setup('exampleDir');

        $handler = new Handler(
            $root->url().\DIRECTORY_SEPARATOR.'Faker/var/',
            $docRepo->reveal(),
            $this->prophesize(WahlkreisRepository::class)->reveal(),
        );
        $handler->delete($document);
    }

    public static function provideUpload(): \Generator
    {
        $wahlkreis = new Wahlkreis();
        $wahlkreis->setName('Foo Stadt');
        $wahlkreis->setState('Berlin');
        $wahlkreis->setId(Ulid::fromString(Ulid::generate()));
        yield [
            fn (Generator $faker) => \App\Dto\Document\Document::new(
                wahlkreis: WahlkreisDto::new($wahlkreis),
                file: FileDto::new(
                    type: 'Landesliste',
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
                    description: $faker->realText()
                )
            ),
            static fn (ObjectProphecy $wahlkreisRepo) => null,
            Document::class,
        ];
    }

    #[DataProvider('provideUpload')]
    public function testHandleUpload(callable $fileUploadCallback, callable $wahlkreisCallback, string $saveClass): void
    {
        $document = new $saveClass();
        $document->setId(Uuid::uuid4());
        $docRepo = $this->prophesize(DocumentsRepository::class);
        $docRepo->save(Argument::type($saveClass))
            ->shouldBeCalled()
            ->will(function ($args) {
                $args[0]->setId(Uuid::uuid4());
            });

        $wahlkreisRepo = $this->prophesize(WahlkreisRepository::class);
        $wahlkreisCallback($wahlkreisRepo);
        $handler = new Handler(
            \dirname(\dirname(__DIR__, 2).\DIRECTORY_SEPARATOR.'Faker/var/'),
            $docRepo->reveal(),
            $wahlkreisRepo->reveal(),
        );
        $handler->handleUploadDocument($fileUploadCallback($this->faker));
    }
}
