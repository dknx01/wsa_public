<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\SupportNumbers;

use App\Dto\Statistic;
use App\Entity\SupportNumbersDirektkandidat;
use App\Entity\SupportNumbersLandesliste;
use App\Entity\User;
use App\Repository\SupportNumbersRepository;
use Doctrine\ORM\Exception\ORMException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

readonly class SupportNumbersService
{
    public function __construct(
        private SupportNumbersRepository $supportNumbersRepo,
        private CacheInterface $numbersCache,
        private SluggerInterface $slugger,
        private Output $output,
    ) {
    }

    /**
     * @throws InvalidArgumentException|ORMException
     */
    public function save(SupportNumbersLandesliste $supportNumber, User|UserInterface $user): void
    {
        $supportNumber->setCreatedBy($user); /**@phpstan-ignore-line*/
        $supportNumber->setUpdatedBy($user); /**@phpstan-ignore-line*/
        $supportNumber->setUpdatedAt(new \DateTimeImmutable());
        $this->supportNumbersRepo->save($supportNumber);
        $this->supportNumbersRepo->refresh($supportNumber);
        $this->handleCache($supportNumber);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getByName(SupportNumbersLandesliste $supportNumber): SupportNumbersItem
    {
        $key = $this->slugger->slug($supportNumber->getName());

        return $this->numbersCache->get($key, fn (ItemInterface $item) => $this->recalculate($supportNumber));
    }

    private function recalculate(SupportNumbersLandesliste $supportNumber): SupportNumbersItem
    {
        return new SupportNumbersItem(...$this->supportNumbersRepo->getNumbersByName($supportNumber->getName()));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function delete(SupportNumbersDirektkandidat|SupportNumbersLandesliste $supportNumber): void
    {
        $supportNumber->setUpdatedBy($supportNumber->getCreatedBy());
        $supportNumber->setDeleted(true);
        $supportNumber->setDeletedAt(new \DateTimeImmutable());
        $this->supportNumbersRepo->save($supportNumber);
        $this->handleCache($supportNumber);
    }

    /**
     * @throws InvalidArgumentException
     */
    private function handleCache(SupportNumbersLandesliste $supportNumber): void
    {
        $key = $this->slugger->slug($supportNumber->getName());
        $this->numbersCache->delete($key);
        $this->numbersCache->get($key, fn (ItemInterface $item) => $this->recalculate($supportNumber));
    }

    /**
     * @return Statistic[]
     */
    public function getByState(string $state): array
    {
        return $this->output->getDataByState($state);
    }
}
