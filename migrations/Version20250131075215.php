<?php

declare(strict_types=1);

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250131075215 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add a type for wahlkreis';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE wahlkreis ADD type VARCHAR(255) NOT NULL, ADD year INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE wahlkreis DROP type, DROP year');
    }
}
