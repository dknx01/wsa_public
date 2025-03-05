<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Entity;

use App\Repository\SupportNumbersRepository;
use App\SupportNumbers\Type;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UlidGenerator;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity(repositoryClass: SupportNumbersRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap([
    Type::LL_BTW->value => self::class,
    Type::DK_BTW->value => SupportNumbersDirektkandidat::class,
    Type::DK_KW->value => SupportNumbersDirektkandidatKommunal::class,
    Type::LL_KW->value => SupportNumbersLandeslisteKommunal::class,
    Type::DK_LTW->value => SupportNumbersDirektkandidatLandtag::class,
    Type::LL_LTW->value => SupportNumbersLandeslisteLandtag::class,
])]
#[ORM\Table(name: 'support_numbers')]
class SupportNumbersLandesliste
{
    public const string TYPE = 'Landesliste';

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UlidGenerator::class)]
    #[ORM\Column(type: 'ulid', unique: true)]
    private ?Ulid $id = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    private \DateTimeImmutable $updatedAt;

    #[ORM\Column(length: 255)]
    private string $name = '';

    #[ORM\Column(options: ['default' => 0])]
    private int $approved = 0;

    #[ORM\Column(options: ['default' => 0])]
    private int $unapproved = 0;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $comment = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Wahlkreis $wahlkreis = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private User $createdBy;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private string $bundesland = '';

    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => false])]
    private bool $deleted = false;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    /**
     * @var Collection<int, Log>
     */
    #[ORM\OneToMany(targetEntity: Log::class, mappedBy: 'supportNumbers', cascade: ['persist'])]
    private Collection $history;

    #[ORM\ManyToOne]
    private ?User $updatedBy = null;

    public function __construct()
    {
        $this->history = new ArrayCollection();
    }

    public function getId(): ?Ulid
    {
        return $this->id;
    }

    public function setId(Ulid $id): static
    {
        $this->id = $id;

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

    public function getType(): string
    {
        return static::TYPE;
    }

    public function setType(string $type): static
    {
        // do nothing
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->updateChanges($this->name, $name, 'name');

        $this->name = $name;

        return $this;
    }

    public function getApproved(): ?int
    {
        return $this->approved;
    }

    public function setApproved(int $approved): static
    {
        $this->updateChanges($this->approved, $approved, 'approved');

        $this->approved = $approved;

        return $this;
    }

    public function getUnapproved(): ?int
    {
        return $this->unapproved;
    }

    public function setUnapproved(int $unapproved): static
    {
        $this->updateChanges($this->unapproved, $unapproved, 'unapproved');

        $this->unapproved = $unapproved;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->updateChanges($this->comment, $comment, 'comment');

        $this->comment = $comment;

        return $this;
    }

    public function getWahlkreis(): ?Wahlkreis
    {
        return $this->wahlkreis;
    }

    public function setWahlkreis(?Wahlkreis $wahlkreis): static
    {
        $this->updateChanges($this->wahlkreis?->getId(), $wahlkreis?->getId(), 'wahlkreis');

        $this->wahlkreis = $wahlkreis;

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

    public function getBundesland(): ?string
    {
        return $this->bundesland;
    }

    public function setBundesland(?string $bundesland): self
    {
        $this->updateChanges($this->bundesland, $bundesland, 'bundesland');

        $this->bundesland = $bundesland;

        return $this;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): self
    {
        $this->updateChanges($this->deleted, $deleted, 'deleted');
        $this->deleted = $deleted;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeImmutable $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    #[ORM\PrePersist]
    public function updateDate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PrePersist]
    public function createDate(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    private function updateChanges(mixed $old, mixed $new, string $field): void
    {
        if ($this->id) {
            $this->addHistory(
                new Log(
                    $this->getUpdatedBy() ?: $this->getCreatedBy(),
                    $this,
                    $this->toString($new),
                    $field,
                    $this->toString($old)
                ));
        }
    }

    /**
     * @return Collection<int, Log>
     */
    public function getHistory(): Collection
    {
        return $this->history;
    }

    public function addHistory(Log $history): static
    {
        if (!$this->history->contains($history)) {
            $this->history->add($history);
            $history->setSupportNumbers($this);
        }

        return $this;
    }

    private function toString(mixed $value): string
    {
        return match (true) {
            null === $value, \is_scalar($value), $value instanceof \Stringable => (string) $value,
            default => 'not stringable',
        };
    }

    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(User $updatedBy): static
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }
}
