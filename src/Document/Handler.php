<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Document;

use App\Entity\Document;
use App\Repository\DocumentsRepository;
use App\Repository\WahlkreisRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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

    public function handleUploadDocument(\App\Dto\Document\Document $documentDto): void
    {
        $document = new Document();
        $document->setType($documentDto->file->type);
        $document->setName(\sprintf('%s %s', $documentDto->wahlkreis->wahlkreis->getName(), $documentDto->wahlkreis->wahlkreis->getState()));
        $document->setDescription($documentDto->file->description);
        $document->setWahlkreis(
            $this->wahlkreisRepository->find($documentDto->wahlkreis->wahlkreis->getId())
        );
        $document->setState($documentDto->wahlkreis->wahlkreis->getState());
        $originalFilename = pathinfo($documentDto->file->file->getClientOriginalName(), \PATHINFO_FILENAME);
        $document->setFileName($originalFilename);
        $this->documentsRepository->save($document);
        $newFilename = $document->getId()->toString().'.'.self::FILE_EXTENSION;
        $documentDto->file->file?->move($this->dir, $newFilename);
    }
}
