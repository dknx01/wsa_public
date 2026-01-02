<?php

declare(strict_types=1);

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251219092831 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add Number log';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE wahlkreis_number_log (id INT AUTO_INCREMENT NOT NULL, changed_at DATETIME NOT NULL, field VARCHAR(255) NOT NULL, old VARCHAR(255) NOT NULL, new VARCHAR(255) NOT NULL, wahlkreis_id CHAR(36) NOT NULL, changed_by_id INT NOT NULL, INDEX IDX_65767AC818DD34B2 (wahlkreis_id), INDEX IDX_65767AC8828AD0A0 (changed_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE wahlkreis_number_log ADD CONSTRAINT FK_65767AC818DD34B2 FOREIGN KEY (wahlkreis_id) REFERENCES wahlkreis_number (id)');
        $this->addSql('ALTER TABLE wahlkreis_number_log ADD CONSTRAINT FK_65767AC8828AD0A0 FOREIGN KEY (changed_by_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE wahlkreis_number_log DROP FOREIGN KEY FK_65767AC818DD34B2');
        $this->addSql('ALTER TABLE wahlkreis_number_log DROP FOREIGN KEY FK_65767AC8828AD0A0');
        $this->addSql('DROP TABLE wahlkreis_number_log');
    }
}
