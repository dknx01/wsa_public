<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Entity;

use App\Dto\WahlkreisType;
use App\Repository\WahlkreisRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UlidGenerator;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity(repositoryClass: WahlkreisRepository::class)]
class Wahlkreis
{
    #[ORM\Id]
    #[ORM\Column(type: 'ulid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UlidGenerator::class)]
    private Ulid $id;

    #[ORM\Column(nullable: true)]
    private ?int $number = null;

    #[ORM\Column(length: 255, nullable: false)]
    private string $name = '';

    #[ORM\Column(length: 255, nullable: false)]
    private string $state;

    #[ORM\Column(type: 'string', length: 255, nullable: false, enumType: WahlkreisType::class)]
    private WahlkreisType $type;

    #[ORM\Column(nullable: false)]
    private int $year;

    #[ORM\Column(type: 'integer', options: ['default' => 200])]
    private int $threshold = 200;

    public function getId(): Ulid
    {
        return $this->id;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(?int $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getType(): WahlkreisType
    {
        return $this->type;
    }

    public function setType(WahlkreisType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function __toString(): string
    {
        if (!$this->getNumber()) {
            return \sprintf('%s - %s', $this->getName(), $this->state);
        }

        return \sprintf('%s (%d) - %s', $this->getName(), $this->getNumber(), $this->state);
    }

    public function setId(Ulid $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getThreshold(): int
    {
        return $this->threshold;
    }

    public function setThreshold(int $threshold): self
    {
        $this->threshold = $threshold;

        return $this;
    }
}
