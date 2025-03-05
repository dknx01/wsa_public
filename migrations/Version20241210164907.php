<?php

declare(strict_types=1);

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241210164907 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add wahlkreis table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE wahlkreis (id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', number INT NOT NULL, name VARCHAR(255) NOT NULL, state VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE wahlkreis');
    }
}
