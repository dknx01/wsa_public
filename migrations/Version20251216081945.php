<?php

declare(strict_types=1);

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251216081945 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add custom Wahlkreistyp table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE wahlkreistyp (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, abbreviation VARCHAR(50) DEFAULT NULL, UNIQUE INDEX UNIQ_7CDD5B9F5E237E06 (name), UNIQUE INDEX UNIQ_7CDD5B9FBCF3411D (abbreviation), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE document ADD archived TINYINT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE support_numbers ADD archived TINYINT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE wahlkreis ADD enabled TINYINT DEFAULT 1 NOT NULL, ADD wahlkreistyp_id INT DEFAULT NULL, CHANGE type type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE wahlkreis ADD CONSTRAINT FK_B51E00DC31231077 FOREIGN KEY (wahlkreistyp_id) REFERENCES wahlkreistyp (id)');
        $this->addSql('CREATE INDEX IDX_B51E00DC31231077 ON wahlkreis (wahlkreistyp_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE wahlkreistyp');
        $this->addSql('ALTER TABLE document DROP archived');
        $this->addSql('ALTER TABLE support_numbers DROP archived');
        $this->addSql('ALTER TABLE wahlkreis DROP FOREIGN KEY FK_B51E00DC31231077');
        $this->addSql('DROP INDEX IDX_B51E00DC31231077 ON wahlkreis');
        $this->addSql('ALTER TABLE wahlkreis DROP enabled, DROP wahlkreistyp_id, CHANGE type type VARCHAR(255) NOT NULL');
    }
}
