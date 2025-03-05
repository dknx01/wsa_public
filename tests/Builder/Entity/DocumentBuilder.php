<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Builder\Entity;

use App\Entity\Document;
use App\Entity\DocumentDirektkandidat;
use App\Entity\DocumentLandesliste;
use App\Entity\Wahlkreis;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class DocumentBuilder extends BaseBuilder
{
    /**
     * @param array{
     *     state: string|null,
     *     name: string|null,
     *     id: UuidInterface|null,
     *     filename: string|null,
     *     description: string|null,
     *     wahlkreis: Wahlkreis|null,
     *     wahlkreis_number: int|null,
     *     wahlkreis_name: string|null
     * } $args
     */
    public static function build(string $type = DocumentLandesliste::TYPE, array $args = []): Document
    {
        return match ($type) {
            DocumentLandesliste::TYPE => self::buildLandesliste($args),
            DocumentDirektkandidat::TYPE => self::buildDirektkandidat($args),
            default => throw new \InvalidArgumentException("Unknown document type $type"),
        };
    }

    /**
     * @param array{
     *     state: string|null,
     *     name: string|null,
     *     id: UuidInterface|null,
     *     filename: string|null,
     *     description: string|null
     * } $args
     */
    private static function buildLandesliste(array $args): DocumentLandesliste
    {
        $document = new DocumentLandesliste();
        $state = $args['state'] ?? self::faker()->bundesland();
        $document->setId($args['id'] ?? Uuid::uuid4());
        $document->setName($args['name'] ?? ('Landliste '.$state));
        $document->setFileName($args['filename'] ?? $document->getName().'1234.pdf');
        $document->setState($state);
        $document->setDescription($args['description'] ?? self::faker()->sentence());

        return $document;
    }

    /**
     * @param array{
     *     state: string|null,
     *     name: string|null,
     *     id: UuidInterface|null,
     *     filename: string|null,
     *     description: string|null,
     *     wahlkreis: Wahlkreis|null,
     *     wahlkreis_number: int|null,
     *     wahlkreis_name: string|null
     * } $args
     */
    private static function buildDirektkandidat(array $args): DocumentDirektkandidat
    {
        $document = new DocumentDirektkandidat();
        $state = $args['state'] ?? self::faker()->bundesland();
        $document->setId($args['id'] ?? Uuid::uuid4());
        $document->setName($args['name'] ?? ('Landliste '.$state));
        $document->setFileName($args['filename'] ?? $document->getName().'1234.pdf');
        $document->setState($state);
        $document->setDescription($args['description'] ?? self::faker()->sentence());
        $document->setWahlkreis($args['wahlkreis'] ?? WahlkreisBuilder::build(
            $args['wahlkreis_number'] ?? null,
            $args['wahlkreis_name'] ?? null
        ));

        return $document;
    }
}
