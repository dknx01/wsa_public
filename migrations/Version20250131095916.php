<?php

declare(strict_types=1);

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250131095916 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'create log table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE log (id BINARY(16) NOT NULL, changed_at DATETIME NOT NULL, field VARCHAR(255) NOT NULL, old VARCHAR(255) DEFAULT NULL, new VARCHAR(255) NOT NULL, changed_by_id INT NOT NULL, support_numbers_id BINARY(16) NOT NULL, INDEX IDX_8F3F68C5828AD0A0 (changed_by_id), INDEX IDX_8F3F68C5D842C8BD (support_numbers_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE log ADD CONSTRAINT FK_8F3F68C5828AD0A0 FOREIGN KEY (changed_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE log ADD CONSTRAINT FK_8F3F68C5D842C8BD FOREIGN KEY (support_numbers_id) REFERENCES support_numbers (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE log DROP FOREIGN KEY FK_8F3F68C5828AD0A0');
        $this->addSql('ALTER TABLE log DROP FOREIGN KEY FK_8F3F68C5D842C8BD');
        $this->addSql('DROP TABLE log');
    }
}
