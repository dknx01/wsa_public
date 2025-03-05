<?php

declare(strict_types=1);

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250221190512 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add permission for each wahlkreis';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE user_wahlkreis (user_id INT NOT NULL, wahlkreis_id BINARY(16) NOT NULL, INDEX IDX_B5FE8D38A76ED395 (user_id), INDEX IDX_B5FE8D3818DD34B2 (wahlkreis_id), PRIMARY KEY(user_id, wahlkreis_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE user_wahlkreis ADD CONSTRAINT FK_B5FE8D38A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_wahlkreis ADD CONSTRAINT FK_B5FE8D3818DD34B2 FOREIGN KEY (wahlkreis_id) REFERENCES wahlkreis (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user_wahlkreis DROP FOREIGN KEY FK_B5FE8D38A76ED395');
        $this->addSql('ALTER TABLE user_wahlkreis DROP FOREIGN KEY FK_B5FE8D3818DD34B2');
        $this->addSql('DROP TABLE user_wahlkreis');
    }
}
