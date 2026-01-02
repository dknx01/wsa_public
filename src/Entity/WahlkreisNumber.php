<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Entity;

use App\Repository\WahlkreisNumberRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity(repositoryClass: WahlkreisNumberRepository::class)]
class WahlkreisNumber
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;

    #[ORM\Column(nullable: true)]
    private ?int $approved = null;

    #[ORM\Column(nullable: true)]
    private ?int $unapproved = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $comment = null;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'wahlkreisNumbers')]
    #[ORM\JoinColumn(nullable: false)]
    private Wahlkreis $wahlkreis;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    private \DateTimeImmutable $updatedAt;

    #[ORM\Column]
    private bool $deleted = false;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private User $createdBy;

    #[ORM\ManyToOne]
    private ?User $updatedBy = null;

    /**
     * @var Collection<int, WahlkreisNumberLog>
     */
    #[ORM\OneToMany(targetEntity: WahlkreisNumberLog::class, mappedBy: 'wahlkreis', orphanRemoval: true)]
    private Collection $changedAt;

    #[ORM\Column(length: 255)]
    private string $name;

    public function __construct()
    {
        $this->changedAt = new ArrayCollection();
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getApproved(): ?int
    {
        return $this->approved;
    }

    public function setApproved(?int $approved): static
    {
        $this->approved = $approved;

        return $this;
    }

    public function getUnapproved(): ?int
    {
        return $this->unapproved;
    }

    public function setUnapproved(?int $unapproved): static
    {
        $this->unapproved = $unapproved;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function isDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): static
    {
        $this->deleted = $deleted;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeImmutable $deletedAt): static
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?User $updatedBy): static
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * @return Collection<int, WahlkreisNumberLog>
     */
    public function getChangedAt(): Collection
    {
        return $this->changedAt;
    }

    public function addChangedAt(WahlkreisNumberLog $changedAt): static
    {
        if (!$this->changedAt->contains($changedAt)) {
            $this->changedAt->add($changedAt);
            $changedAt->setWahlkreisNumber($this);
        }

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
}
