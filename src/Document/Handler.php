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
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Ulid;

readonly class Handler
{
    private const string FILE_EXTENSION = 'pdf';

    public function __construct(
        #[Autowire('%kernel.project_dir%/var/docs/')] private string $dir,
        private DocumentsRepository $documentsRepository,
        private WahlkreisRepository $wahlkreisRepository,
    ) {
    }

    /**
     * @throws IOException When removal fails
     */
    public function delete(Document $document): void
    {
        $fs = new Filesystem();
        $fileName = $document->getId()->toString().'.'.self::FILE_EXTENSION;
        $fs->remove(\sprintf('%s/%s', $this->dir, $fileName));
        $this->documentsRepository->delete($document);
    }

    /**
     * @throws IOException   When removal fails
     * @throws FileException
     */
    public function handleEdit(Document $document, ?UploadedFile $fileUpload): void
    {
        if ($fileUpload) {
            $originalFilename = pathinfo($fileUpload->getClientOriginalName(), \PATHINFO_FILENAME);
            $fileName = $document->getId()->toString().'.'.self::FILE_EXTENSION;
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
        $doc->setName(\sprintf('Landesliste %s', $fileUpload->state));
        $doc->setState($fileUpload->state);
        $doc->setDescription($fileUpload->description);
        $originalFilename = pathinfo($fileUpload->file->getClientOriginalName(), \PATHINFO_FILENAME);
        $doc->setFileName($originalFilename);
        $this->documentsRepository->save($doc);

        $newFilename = $doc->getId()->toString().'.'.self::FILE_EXTENSION;
        $fileUpload->file?->move($this->dir, $newFilename);
    }

    private function handleDirektkandidatBtw(FileUploadDirektkandidat $fileUpload): void
    {
        $doc = new DocumentDirektkandidat();
        $doc->setWahlkreis($this->wahlkreisRepository->find(Ulid::fromString($fileUpload->area)));
        $doc->setName(\sprintf('Direktkandidat %s', $doc->getWkName()));
        $doc->setState($fileUpload->state);
        $doc->setDescription($fileUpload->description);
        $originalFilename = pathinfo($fileUpload->file->getClientOriginalName(), \PATHINFO_FILENAME);
        $doc->setFileName($originalFilename);
        $this->documentsRepository->save($doc);

        $newFilename = $doc->getId()->toString().'.'.self::FILE_EXTENSION;
        $fileUpload->file->move($this->dir, $newFilename);
    }
}
