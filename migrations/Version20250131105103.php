<?php

declare(strict_types=1);

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250131105103 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add user to support number';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE support_numbers CHANGE updated_by_id updated_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE support_numbers ADD CONSTRAINT FK_CAABC205896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_CAABC205896DBBDE ON support_numbers (updated_by_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE support_numbers DROP FOREIGN KEY FK_CAABC205896DBBDE');
        $this->addSql('DROP INDEX IDX_CAABC205896DBBDE ON support_numbers');
        $this->addSql('ALTER TABLE support_numbers CHANGE updated_by_id updated_by_id INT NOT NULL');
    }
}
