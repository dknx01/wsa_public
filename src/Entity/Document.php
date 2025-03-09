<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Entity;

use App\Repository\DocumentsRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity(repositoryClass: DocumentsRepository::class)]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap([
    DocumentLandesliste::TYPE => DocumentLandesliste::class,
    DocumentDirektkandidat::TYPE => DocumentDirektkandidat::class,
])]
class Document
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(length: 255)]
    private string $state;

    #[ORM\Column(length: 255)]
    private string $fileName;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne]
    private ?Wahlkreis $wahlkreis = null;

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function setId(UuidInterface $id): static
    {
        $this->id = $id;

        return $this;
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

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): static
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getWkNr(): ?int
    {
        return $this->wahlkreis?->getNumber();
    }

    public function getWkName(): ?string
    {
        return match ($this->wahlkreis) {
            null => null,
            default => null !== $this->wahlkreis->getNumber()
                ? \sprintf('%s (Nr. %d)', $this->wahlkreis->getName(), $this->wahlkreis->getNumber())
                : \sprintf('%s', $this->wahlkreis->getName()),
        };
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): void
    {
        $this->state = $state;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getWahlkreis(): ?Wahlkreis
    {
        return $this->wahlkreis;
    }

    public function setWahlkreis(?Wahlkreis $wahlkreis): static
    {
        $this->wahlkreis = $wahlkreis;

        return $this;
    }
}
