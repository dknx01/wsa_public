<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Contracts;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Repository\RepositoryFactory;
use Doctrine\Persistence\ObjectRepository;

abstract class RepositoryAwareMigration extends AbstractMigration
{
    private RepositoryFactory $factory;
    protected EntityManagerInterface $em;

    public function setFactory(RepositoryFactory $factory): void
    {
        $this->factory = $factory;
    }

    public function setEm(EntityManagerInterface $em): void
    {
        $this->em = $em;
    }

    /* @phpstan-ignore-next-line */
    public function getRepository(string $className): ObjectRepository
    {
        return $this->factory->getRepository($this->em, $className);
    }
}
