<?php

declare(strict_types=1);

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250301173134 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add threshold for wahlkreis';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE wahlkreis ADD threshold INT DEFAULT 200 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE wahlkreis DROP threshold');
    }
}
