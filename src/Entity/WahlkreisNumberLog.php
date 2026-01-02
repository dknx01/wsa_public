<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Entity;

use App\Repository\WahlkreisNumberLogRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WahlkreisNumberLogRepository::class)]
class WahlkreisNumberLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'changedAt')]
    #[ORM\JoinColumn(nullable: false)]
    private WahlkreisNumber $wahlkreis;

    #[ORM\Column]
    private \DateTimeImmutable $changedAt;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private User $changedBy;

    /**
     * @var array<string, array<'old'|'new', mixed>>
     */
    #[ORM\Column()]
    private array $changes = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWahlkreisNumber(): WahlkreisNumber
    {
        return $this->wahlkreis;
    }

    public function setWahlkreisNumber(WahlkreisNumber $wahlkreis): static
    {
        $this->wahlkreis = $wahlkreis;

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

    public function getChangedBy(): User
    {
        return $this->changedBy;
    }

    public function setChangedBy(User $changedBy): static
    {
        $this->changedBy = $changedBy;

        return $this;
    }

    /**
     * @return array<string, array<'old'|'new', mixed>>
     */
    public function getChanges(): array
    {
        return $this->changes;
    }

    /**
     * @param array<string, array<'old'|'new', mixed>> $changes
     *
     * @return $this
     */
    public function setChanges(array $changes): static
    {
        $this->changes = $changes;

        return $this;
    }
}
