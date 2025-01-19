<?php

declare(strict_types=1);

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241210195335 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add wahlkreis to document';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE document ADD wahlkreis_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:ulid)\'');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A7618DD34B2 FOREIGN KEY (wahlkreis_id) REFERENCES wahlkreis (id)');
        $this->addSql('CREATE INDEX IDX_D8698A7618DD34B2 ON document (wahlkreis_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A7618DD34B2');
        $this->addSql('DROP INDEX IDX_D8698A7618DD34B2 ON document');
        $this->addSql('ALTER TABLE document DROP wahlkreis_id');
    }
}
