<?php

declare(strict_types=1);

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251219091541 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add new Number table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE wahlkreis_number (id CHAR(36) NOT NULL, approved INT DEFAULT NULL, unapproved INT DEFAULT NULL, comment VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted TINYINT NOT NULL, deleted_at DATETIME DEFAULT NULL, wahlkreis_id BINARY(16) NOT NULL, created_by_id INT NOT NULL, updated_by_id INT DEFAULT NULL, INDEX IDX_F6BC06AD18DD34B2 (wahlkreis_id), INDEX IDX_F6BC06ADB03A8386 (created_by_id), INDEX IDX_F6BC06AD896DBBDE (updated_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE wahlkreis_number ADD CONSTRAINT FK_F6BC06AD18DD34B2 FOREIGN KEY (wahlkreis_id) REFERENCES wahlkreis (id)');
        $this->addSql('ALTER TABLE wahlkreis_number ADD CONSTRAINT FK_F6BC06ADB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE wahlkreis_number ADD CONSTRAINT FK_F6BC06AD896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE wahlkreis_number DROP FOREIGN KEY FK_F6BC06AD18DD34B2');
        $this->addSql('ALTER TABLE wahlkreis_number DROP FOREIGN KEY FK_F6BC06ADB03A8386');
        $this->addSql('ALTER TABLE wahlkreis_number DROP FOREIGN KEY FK_F6BC06AD896DBBDE');
        $this->addSql('DROP TABLE wahlkreis_number');
    }
}
