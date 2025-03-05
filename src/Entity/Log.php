<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Entity;

use App\Repository\LogRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UlidGenerator;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity(repositoryClass: LogRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Log
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UlidGenerator::class)]
    #[ORM\Column(type: 'ulid', unique: true)]
    private ?Ulid $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private User $changedBy;

    #[ORM\Column]
    private \DateTimeImmutable $changedAt;

    #[ORM\Column(length: 255)]
    private string $field;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $old = null;

    #[ORM\Column(length: 255)]
    private string $new;

    #[ORM\ManyToOne(inversedBy: 'history')]
    #[ORM\JoinColumn(nullable: false)]
    private SupportNumbersLandesliste $supportNumbers;

    public function __construct(User $changedBy, SupportNumbersLandesliste $supportNumbers, string $new, string $field, ?string $old = null)
    {
        $this->changedBy = $changedBy;
        $this->supportNumbers = $supportNumbers;
        $this->new = $new;
        $this->old = $old;
        $this->field = $field;
    }

    public function getId(): Ulid
    {
        return $this->id;
    }

    public function setId(Ulid $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getChangedBy(): ?User
    {
        return $this->changedBy;
    }

    public function setChangedBy(User $changedBy): static
    {
        $this->changedBy = $changedBy;

        return $this;
    }

    public function getChangedAt(): \DateTimeImmutable
    {
        return $this->changedAt;
    }

    public function setChangedAt(\DateTimeImmutable $changedAt): static
    {
        $this->changedAt = $changedAt;

        return $this;
    }

    public function getField(): ?string
    {
        return $this->field;
    }

    public function setField(string $field): static
    {
        $this->field = $field;

        return $this;
    }

    public function getOld(): ?string
    {
        return $this->old;
    }

    public function setOld(?string $old): static
    {
        $this->old = $old;

        return $this;
    }

    public function getNew(): ?string
    {
        return $this->new;
    }

    public function setNew(string $new): static
    {
        $this->new = $new;

        return $this;
    }

    public function getSupportNumbers(): SupportNumbersLandesliste
    {
        return $this->supportNumbers;
    }

    public function setSupportNumbers(SupportNumbersLandesliste $supportNumbers): static
    {
        $this->supportNumbers = $supportNumbers;

        return $this;
    }

    #[ORM\PrePersist]
    public function updateDate(): void
    {
        $this->changedAt = new \DateTimeImmutable();
    }
}
