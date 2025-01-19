<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Entity;

use App\Repository\SupportNumbersRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SupportNumbersRepository::class)]
class SupportNumbersDirektkandidat extends SupportNumbersLandesliste
{
    public const string TYPE = 'Direktkandidat';
}
