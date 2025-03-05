<?php

declare(strict_types=1);

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250120174524 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add bundesland to support numbers';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE support_numbers ADD bundesland VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE support_numbers DROP bundesland');
    }
}
