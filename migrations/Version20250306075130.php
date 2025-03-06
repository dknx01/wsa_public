<?php

declare(strict_types=1);

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250306075130 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Crate all tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE document (id CHAR(36) NOT NULL, name VARCHAR(255) NOT NULL, state VARCHAR(255) NOT NULL, file_name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, wahlkreis_id BINARY(16) DEFAULT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_D8698A7618DD34B2 (wahlkreis_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE log (id BINARY(16) NOT NULL, changed_at DATETIME NOT NULL, field VARCHAR(255) NOT NULL, old VARCHAR(255) DEFAULT NULL, new VARCHAR(255) NOT NULL, changed_by_id INT NOT NULL, support_numbers_id BINARY(16) NOT NULL, INDEX IDX_8F3F68C5828AD0A0 (changed_by_id), INDEX IDX_8F3F68C5D842C8BD (support_numbers_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE statistic (id CHAR(36) NOT NULL, name VARCHAR(255) NOT NULL, bundesland VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, approved INT NOT NULL, unapproved INT NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE support_numbers (id BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, name VARCHAR(255) NOT NULL, approved INT DEFAULT 0 NOT NULL, unapproved INT DEFAULT 0 NOT NULL, comment VARCHAR(255) DEFAULT NULL, bundesland VARCHAR(255) NOT NULL, deleted TINYINT(1) DEFAULT 0 NOT NULL, deleted_at DATE DEFAULT NULL, wahlkreis_id BINARY(16) DEFAULT NULL, created_by_id INT NOT NULL, updated_by_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_CAABC20518DD34B2 (wahlkreis_id), INDEX IDX_CAABC205B03A8386 (created_by_id), INDEX IDX_CAABC205896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_active TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_wahlkreis (user_id INT NOT NULL, wahlkreis_id BINARY(16) NOT NULL, INDEX IDX_B5FE8D38A76ED395 (user_id), INDEX IDX_B5FE8D3818DD34B2 (wahlkreis_id), PRIMARY KEY(user_id, wahlkreis_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE verbandsseiten (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, link VARCHAR(255) DEFAULT NULL, link_name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE wahlkreis (id BINARY(16) NOT NULL, number INT NOT NULL, name VARCHAR(255) NOT NULL, state VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, year INT NOT NULL, threshold INT DEFAULT 200 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE support_numbers_cache (item_id VARBINARY(255) NOT NULL, item_data MEDIUMBLOB NOT NULL, item_lifetime INT UNSIGNED DEFAULT NULL, item_time INT UNSIGNED NOT NULL, PRIMARY KEY(item_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A7618DD34B2 FOREIGN KEY (wahlkreis_id) REFERENCES wahlkreis (id)');
        $this->addSql('ALTER TABLE log ADD CONSTRAINT FK_8F3F68C5828AD0A0 FOREIGN KEY (changed_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE log ADD CONSTRAINT FK_8F3F68C5D842C8BD FOREIGN KEY (support_numbers_id) REFERENCES support_numbers (id)');
        $this->addSql('ALTER TABLE support_numbers ADD CONSTRAINT FK_CAABC20518DD34B2 FOREIGN KEY (wahlkreis_id) REFERENCES wahlkreis (id)');
        $this->addSql('ALTER TABLE support_numbers ADD CONSTRAINT FK_CAABC205B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE support_numbers ADD CONSTRAINT FK_CAABC205896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_wahlkreis ADD CONSTRAINT FK_B5FE8D38A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_wahlkreis ADD CONSTRAINT FK_B5FE8D3818DD34B2 FOREIGN KEY (wahlkreis_id) REFERENCES wahlkreis (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A7618DD34B2');
        $this->addSql('ALTER TABLE log DROP FOREIGN KEY FK_8F3F68C5828AD0A0');
        $this->addSql('ALTER TABLE log DROP FOREIGN KEY FK_8F3F68C5D842C8BD');
        $this->addSql('ALTER TABLE support_numbers DROP FOREIGN KEY FK_CAABC20518DD34B2');
        $this->addSql('ALTER TABLE support_numbers DROP FOREIGN KEY FK_CAABC205B03A8386');
        $this->addSql('ALTER TABLE support_numbers DROP FOREIGN KEY FK_CAABC205896DBBDE');
        $this->addSql('ALTER TABLE user_wahlkreis DROP FOREIGN KEY FK_B5FE8D38A76ED395');
        $this->addSql('ALTER TABLE user_wahlkreis DROP FOREIGN KEY FK_B5FE8D3818DD34B2');
        $this->addSql('DROP TABLE document');
        $this->addSql('DROP TABLE log');
        $this->addSql('DROP TABLE statistic');
        $this->addSql('DROP TABLE support_numbers');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_wahlkreis');
        $this->addSql('DROP TABLE verbandsseiten');
        $this->addSql('DROP TABLE wahlkreis');
        $this->addSql('DROP TABLE support_numbers_cache');
    }
}
