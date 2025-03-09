<?php

declare(strict_types=1);

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250308104304 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Make wahlkreis number nullable';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE wahlkreis CHANGE number number INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE wahlkreis CHANGE number number INT NOT NULL');
    }
}
