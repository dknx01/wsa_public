<?php

declare(strict_types=1);

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250306075421 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add admin user';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'INSERT INTO user (id, email, password, is_active, roles) VALUES (?, ?, ?, ?, ?)',
            [
                1, 'admin@wsa.test', '$2y$13$NXj6NlKzaOls94cwSBvLn.kVjlE61oLPwkmpPrprArirciQ.tCR96', true, '["ROLE_ADMIN"]',
            ]
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM user WHERE email = ?', ['admin@wsa.test']);
    }
}
