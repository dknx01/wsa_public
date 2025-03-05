<?php

declare(strict_types=1);

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241127222235 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adding Verbandsseiten link name';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE verbandsseiten ADD link_name VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE verbandsseiten DROP link_name');
    }
}
