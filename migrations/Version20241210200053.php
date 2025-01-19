<?php

declare(strict_types=1);

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241210200053 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove not needed columns';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE document DROP wk_nr, DROP wk_name');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE document ADD wk_nr INT DEFAULT NULL, ADD wk_name VARCHAR(255) DEFAULT NULL');
    }
}
