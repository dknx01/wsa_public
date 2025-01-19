<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Unit\Migrations\Factory;

use App\Contracts\RepositoryAwareMigration;
use App\Migrations\Factory\MigrationFactoryDecorator;
use Doctrine\Migrations\Version\MigrationFactory;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Repository\RepositoryFactory;
use PHPUnit\Framework\TestCase;

class MigrationFactoryDecoratorTest extends TestCase
{
    public function testCreateVersion(): void
    {
        $migration = $this->createMock(RepositoryAwareMigration::class);

        $factory = $this->createMock(MigrationFactory::class);
        $factory->expects($this->once())
            ->method('createVersion')
            ->with('Foo\bar')
            ->willReturn($migration);

        $repoFactory = $this->createMock(RepositoryFactory::class);
        $config = $this->createMock(Configuration::class);
        $config->expects($this->once())
            ->method('getRepositoryFactory')
            ->willReturn($repoFactory);

        $em = $this->createMock(EntityManager::class);

        $migrationFactory = new MigrationFactoryDecorator(
            $factory,
            $config,
            $em
        );

        $migration = $migrationFactory->createVersion(
            'Foo\bar'
        );
    }
}
