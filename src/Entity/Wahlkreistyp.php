<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Entity;

use App\Repository\WahlkreistypRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WahlkreistypRepository::class)]
class Wahlkreistyp
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private string $name;

    #[ORM\Column(length: 50, unique: true, nullable: true)]
    private ?string $abbreviation = null;

    public function __construct(?string $name = null, ?string $abbreviation = null)
    {
        if (null !== $name) {
            $this->name = $name;
        }
        if (null !== $abbreviation) {
            $this->abbreviation = $abbreviation;
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getAbbreviation(): ?string
    {
        return $this->abbreviation;
    }

    public function setAbbreviation(?string $abbreviation): static
    {
        $this->abbreviation = $abbreviation;

        return $this;
    }
}
