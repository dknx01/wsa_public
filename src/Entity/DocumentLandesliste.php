<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Entity;

use App\Repository\DocumentsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DocumentsRepository::class)]
class DocumentLandesliste extends Document
{
    public const string TYPE = 'Landesliste';

    public function getType(): string
    {
        return self::TYPE;
    }
}
