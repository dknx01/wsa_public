<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Entity;

use App\Repository\SupportNumbersRepository;
use App\SupportNumbers\Type;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SupportNumbersRepository::class)]
class SupportNumbersDirektkandidatLandtag extends SupportNumbersLandesliste
{
    public const string TYPE = Type::DK_LTW->value;
}
