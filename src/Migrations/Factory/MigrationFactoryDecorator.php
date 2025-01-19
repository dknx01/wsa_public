<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Migrations\Factory;

use App\Contracts\RepositoryAwareMigration;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\Migrations\Version\MigrationFactory;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManagerInterface;

readonly class MigrationFactoryDecorator implements MigrationFactory
{
    public function __construct(
        private MigrationFactory $migrationFactory,
        private Configuration $configuration,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function createVersion(string $migrationClassName): AbstractMigration
    {
        $instance = $this->migrationFactory->createVersion($migrationClassName);
        if ($instance instanceof RepositoryAwareMigration) {
            $instance->setFactory($this->configuration->getRepositoryFactory());
            $instance->setEm($this->entityManager);
        }

        return $instance;
    }
}
