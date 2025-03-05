<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Document;

use App\Dto\Api\FileUpload;
use App\Dto\Api\FileUploadDirektkandidat;
use App\Entity\Document;
use App\Entity\DocumentDirektkandidat;
use App\Entity\DocumentLandesliste;
use App\Repository\DocumentsRepository;
use App\Repository\WahlkreisRepository;
use Ramsey\Uuid\Uuid;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Ulid;

readonly class Handler
{
    public function __construct(
        #[Autowire('%kernel.project_dir%/var/docs/')] private string $dir,
        private DocumentsRepository $documentsRepository,
        private WahlkreisRepository $wahlkreisRepository,
    ) {
    }

    public function handleEdit(Document $document, ?UploadedFile $fileUpload): void
    {
        if ($fileUpload) {
            $originalFilename = pathinfo($fileUpload->getClientOriginalName(), \PATHINFO_FILENAME);
            $fileName = $document->getId()->toString().'.'.$fileUpload->guessExtension();
            $document->setFileName($originalFilename);
            $fs = new Filesystem();
            $fs->remove(\sprintf('%s/%s', $this->dir, $fileName));
            $fileUpload->move($this->dir, $fileName);
        }
        $this->documentsRepository->save($document);
        $this->documentsRepository->refresh($document);
    }

    /**
     * @throws UnsupportedDocumentTypeException
     */
    public function handleUpload(FileUpload|FileUploadDirektkandidat $fileUpload, DocumentType|string $type): void
    {
        match ($type) {
            DocumentType::LANDESLISTE, DocumentType::LANDESLISTE->value => $this->handleLandesliste($fileUpload),
            DocumentType::DIREKTKANDIDAT_BTW, DocumentType::DIREKTKANDIDAT_BTW->value => $this->handleDirektkandidatBtw($fileUpload),
            default => throw new UnsupportedDocumentTypeException($type),
        };
    }

    private function handleLandesliste(FileUpload $fileUpload): void
    {
        $doc = new DocumentLandesliste();
        $doc->setId(Uuid::uuid4());
        $doc->setName(\sprintf('Landesliste %s', $fileUpload->state));
        $doc->setState($fileUpload->state);
        $doc->setDescription($fileUpload->description);
        $originalFilename = pathinfo($fileUpload->file->getClientOriginalName(), \PATHINFO_FILENAME);
        $newFilename = $doc->getId()->toString().'.'.$fileUpload->file->guessExtension();
        $doc->setFileName($originalFilename);

        $fileUpload->file?->move($this->dir, $newFilename);
        $this->documentsRepository->save($doc);
    }

    private function handleDirektkandidatBtw(FileUploadDirektkandidat $fileUpload): void
    {
        $doc = new DocumentDirektkandidat();
        $doc->setId(Uuid::uuid4());
        $doc->setWahlkreis($this->wahlkreisRepository->find(Ulid::fromString($fileUpload->area)));
        $doc->setName(\sprintf('Direktkandidat %s', $doc->getWkName()));
        $doc->setState($fileUpload->state);
        $doc->setDescription($fileUpload->description);
        $originalFilename = pathinfo($fileUpload->file->getClientOriginalName(), \PATHINFO_FILENAME);
        $newFilename = $doc->getId()->toString().'.'.$fileUpload->file->guessExtension();
        $doc->setFileName($originalFilename);
        $fileUpload->file->move($this->dir, $newFilename);
        $this->documentsRepository->save($doc);
    }
}
