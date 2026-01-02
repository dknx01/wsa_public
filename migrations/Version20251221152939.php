<?php

declare(strict_types=1);

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251221152939 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'New WahlkreisNumber log';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE wahlkreis_number_log DROP FOREIGN KEY FK_65767AC818DD34B2');
        $this->addSql('ALTER TABLE wahlkreis_number_log DROP FOREIGN KEY FK_65767AC8828AD0A0');
        $this->addSql('DROP TABLE wahlkreis_number_log');
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException();
    }
}
