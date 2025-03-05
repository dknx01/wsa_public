<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Builder\Entity;

use App\Entity\SupportNumbersDirektkandidat;
use App\Entity\SupportNumbersLandesliste;
use App\Entity\User;
use App\Entity\Wahlkreis;
use Symfony\Component\Uid\Ulid;

class SupportNumbersBuilder extends BaseBuilder
{
    /**
     * @param array{
     *     bundesland: string|null,
     *     approved: int|null,
     *     unapproved: int|null,
     *     user: User|null,
     *     name: string|null,
     *     id: Ulid|null,
     *     deleted: bool|null,
     *     deletedAt: \DateTimeImmutable|null,
     *     wahlkreis: Wahlkreis|null,
     *     wahlkreis_number: int|null,
     *     wahlkreis_name: string|null,
     *     updatedAt: \DateTimeImmutable|null
     * } $data
     */
    public static function build(string $type = SupportNumbersLandesliste::TYPE, array $data = []): SupportNumbersLandesliste|SupportNumbersDirektkandidat
    {
        return match ($type) {
            SupportNumbersLandesliste::TYPE => self::buildLandesliste($data),
            SupportNumbersDirektkandidat::TYPE => self::buildDirektkandidat($data),
            default => throw new \InvalidArgumentException("Unknown type $type"),
        };
    }

    /**
     * @param array{
     *     bundesland: string|null,
     *     approved: int|null,
     *     unapproved: int|null,
     *     user: User|null,
     *     name: string|null,
     *     id: Ulid|null,
     *     deleted: bool|null,
     *     deletedAt: \DateTimeImmutable|null,
     *     wahlkreis: Wahlkreis|null,
     *     wahlkreis_number: int|null,
     *     wahlkreis_name: string|null,
     *     updatedAt: \DateTimeImmutable|null
     * } $data
     */
    private static function buildLandesliste(array $data): SupportNumbersLandesliste
    {
        $supportNumber = new SupportNumbersLandesliste();
        $supportNumber->setBundesland($data['bundesland'] ?? self::faker()->bundesland());
        $supportNumber->setName($data['name'] ?? \sprintf('Landesliste %s', $supportNumber->getBundesland()));
        $supportNumber->setApproved($data['approved'] ?? 0);
        $supportNumber->setUnapproved($data['unapproved'] ?? 0);
        $supportNumber->setCreatedBy($data['user'] ?? UserBuilder::build());
        if (\array_key_exists('id', $data)) {
            $supportNumber->setId($data['id']);
        }
        if (\array_key_exists('deleted', $data)) {
            $supportNumber->setDeleted($data['deleted']);
        }
        if (\array_key_exists('deletedAt', $data)) {
            $supportNumber->setDeletedAt($data['deletedAt']);
        }
        if (\array_key_exists('updatedAt', $data)) {
            $supportNumber->setDeletedAt($data['updatedAt']);
        }

        return $supportNumber;
    }

    /**
     * @param array{
     *     bundesland: string|null,
     *     approved: int|null,
     *     unapproved: int|null,
     *     user: User|null,
     *     name: string|null,
     *     id: Ulid|null,
     *     deleted: bool|null,
     *     deletedAt: \DateTimeImmutable|null,
     *     wahlkreis: Wahlkreis|null,
     *     wahlkreis_number: int|null,
     *     wahlkreis_name: string|null,
     *     updatedAt: \DateTimeImmutable|null
     * } $data
     */
    private static function buildDirektkandidat(array $data): SupportNumbersDirektkandidat
    {
        $supportNumber = new SupportNumbersDirektkandidat();
        $supportNumber->setBundesland($data['bundesland'] ?? self::faker()->bundesland());
        $supportNumber->setName(
            $data['name'] ??
                \sprintf(
                    'Direktkandidat %s',
                    $supportNumber->getWahlkreis()?->getName() ?: ($data['wahlkreis_name'] ?? ''))
        );
        $supportNumber->setApproved($data['approved'] ?? 0);
        $supportNumber->setUnapproved($data['unapproved'] ?? 0);
        $supportNumber->setCreatedBy($data['user'] ?? UserBuilder::build());

        if (\array_key_exists('id', $data)) {
            $supportNumber->setId($data['id']);
        }
        if (\array_key_exists('deleted', $data)) {
            $supportNumber->setDeleted($data['deleted']);
        }
        if (\array_key_exists('deletedAt', $data)) {
            $supportNumber->setDeletedAt($data['deletedAt']);
        }
        if (\array_key_exists('updatedAt', $data)) {
            $supportNumber->setDeletedAt($data['updatedAt']);
        }
        $supportNumber->setWahlkreis(
            $data['wahlkreis'] ??
                WahlkreisBuilder::build($data['wahlkreis_number'] ?? null, $data['wahlkreis_name'] ?? null)
        );

        return $supportNumber;
    }
}
