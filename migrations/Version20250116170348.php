<?php

declare(strict_types=1);

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250116170348 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Combine support numbers with wahlkreis table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE support_numbers DROP FOREIGN KEY FK_CAABC2055D83CC1');
        $this->addSql('DROP INDEX IDX_CAABC2055D83CC1 ON support_numbers');
        $this->addSql('ALTER TABLE support_numbers CHANGE state_id wahlkreis_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:ulid)\'');
        $this->addSql('ALTER TABLE support_numbers ADD CONSTRAINT FK_CAABC20518DD34B2 FOREIGN KEY (wahlkreis_id) REFERENCES wahlkreis (id)');
        $this->addSql('CREATE INDEX IDX_CAABC20518DD34B2 ON support_numbers (wahlkreis_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE support_numbers DROP FOREIGN KEY FK_CAABC20518DD34B2');
        $this->addSql('DROP INDEX IDX_CAABC20518DD34B2 ON support_numbers');
        $this->addSql('ALTER TABLE support_numbers CHANGE wahlkreis_id state_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:ulid)\'');
        $this->addSql('ALTER TABLE support_numbers ADD CONSTRAINT FK_CAABC2055D83CC1 FOREIGN KEY (state_id) REFERENCES wahlkreis (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_CAABC2055D83CC1 ON support_numbers (state_id)');
    }
}
