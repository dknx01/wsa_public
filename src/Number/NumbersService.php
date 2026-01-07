<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Number;

use App\Dto\Statistic;
use App\Entity\User;
use App\Entity\WahlkreisNumber;
use App\Repository\WahlkreisNumberRepository;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

readonly class NumbersService
{
    public function __construct(
        private WahlkreisNumberRepository $wahlkreisNumberRepository,
        private CacheInterface $numbersCache,
        private SluggerInterface $slugger,
        private Output $output,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function save(NumberDto $numberDto, User|UserInterface $user): void
    {
        $wahlkreisNumber = new WahlkreisNumber();
        $wahlkreisNumber->setName($numberDto->name);
        $wahlkreisNumber->setWahlkreis($numberDto->wahlkreis);
        $wahlkreisNumber->setCreatedAt(\DateTimeImmutable::createFromInterface($numberDto->updatedAt));
        $wahlkreisNumber->setApproved($numberDto->approved ? (int) $numberDto->approved : null);
        $wahlkreisNumber->setUnapproved($numberDto->unapproved ? (int) $numberDto->unapproved : null);
        $wahlkreisNumber->setComment($numberDto->comment);
        $wahlkreisNumber->setCreatedBy($user); /**@phpstan-ignore-line*/
        $wahlkreisNumber->setUpdatedBy($user); /**@phpstan-ignore-line*/
        $wahlkreisNumber->setUpdatedAt(new \DateTimeImmutable());
        $this->wahlkreisNumberRepository->save($wahlkreisNumber);
        $this->wahlkreisNumberRepository->refresh($wahlkreisNumber);
        $this->handleCache($wahlkreisNumber);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getByName(WahlkreisNumber $wahlkreisNumber): NumbersItem
    {
        $key = $this->slugger->slug($wahlkreisNumber->getName());

        return $this->numbersCache->get($key, fn (ItemInterface $item) => $this->recalculate($wahlkreisNumber));
    }

    private function recalculate(WahlkreisNumber $wahlkreisNumber): NumbersItem
    {
        return new NumbersItem(...$this->wahlkreisNumberRepository->getNumbersByName($wahlkreisNumber->getName()));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function delete(WahlkreisNumber $wahlkreisNumber): void
    {
        $wahlkreisNumber->setUpdatedBy($wahlkreisNumber->getCreatedBy());
        $wahlkreisNumber->setDeleted(true);
        $wahlkreisNumber->setDeletedAt(new \DateTimeImmutable());
        $this->wahlkreisNumberRepository->save($wahlkreisNumber);
        $this->handleCache($wahlkreisNumber);
    }

    /**
     * @throws InvalidArgumentException
     */
    private function handleCache(WahlkreisNumber $wahlkreisNumber): void
    {
        $key = $this->slugger->slug($wahlkreisNumber->getName());
        $this->numbersCache->delete($key);
        $this->numbersCache->get($key, fn (ItemInterface $item) => $this->recalculate($wahlkreisNumber));
    }

    /**
     * @return Statistic[]
     *
     * @throws InvalidArgumentException
     */
    public function getByState(string $state): array
    {
        return $this->output->getDataByState($state);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function saveEntity(WahlkreisNumber $wahlkreisNumber, ?UserInterface $user): void
    {
        $wahlkreisNumber->setupdatedAt(new \DateTimeImmutable());
        $wahlkreisNumber->setUpdatedBy($user); /* @phpstan-ignore argument.type */
        $this->wahlkreisNumberRepository->save($wahlkreisNumber);
        $this->handleCache($wahlkreisNumber);
    }
}
