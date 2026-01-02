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
use Prophecy\PhpUnit\ProphecyTrait;

class MigrationFactoryDecoratorTest extends TestCase
{
    use ProphecyTrait;

    public function testCreateVersion(): void
    {
        $migration = $this->prophesize(RepositoryAwareMigration::class);

        $factory = $this->prophesize(MigrationFactory::class);
        $factory->createVersion('Foo\bar')
            ->shouldBeCalledOnce()
            ->willReturn($migration->reveal());

        $repoFactory = $this->prophesize(RepositoryFactory::class);
        $config = $this->prophesize(Configuration::class);
        $config->getRepositoryFactory()
            ->shouldBeCalledOnce()
            ->willReturn($repoFactory);

        $em = $this->prophesize(EntityManager::class);

        $migrationFactory = new MigrationFactoryDecorator(
            $factory->reveal(),
            $config->reveal(),
            $em->reveal()
        );

        $migrationFactory->createVersion(
            'Foo\bar'
        );
    }
}
