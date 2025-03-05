<?php

declare(strict_types=1);

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250115124823 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Table for support numbers';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE support_numbers (id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', state_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:ulid)\', created_by_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', type VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, approved INT DEFAULT 0 NOT NULL, unapproved INT DEFAULT 0 NOT NULL, comment VARCHAR(255) DEFAULT NULL, INDEX IDX_CAABC2055D83CC1 (state_id), INDEX IDX_CAABC205B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE support_numbers ADD CONSTRAINT FK_CAABC2055D83CC1 FOREIGN KEY (state_id) REFERENCES wahlkreis (id)');
        $this->addSql('ALTER TABLE support_numbers ADD CONSTRAINT FK_CAABC205B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE support_numbers DROP FOREIGN KEY FK_CAABC2055D83CC1');
        $this->addSql('ALTER TABLE support_numbers DROP FOREIGN KEY FK_CAABC205B03A8386');
        $this->addSql('DROP TABLE support_numbers');
    }
}
