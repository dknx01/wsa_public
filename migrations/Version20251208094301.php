<?php

declare(strict_types=1);

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251208094301 extends AbstractMigration
{
    /** @var array<string, string> */
    private const array DATA = [
        'BTW' => 'Bundestagswahl',
        'LTW' => 'Landtagswahl',
        'KW' => 'Kommunalwahl',
        'KW_KOMMUNE' => 'Kommunalwahl - Kommune',
        'KW_KREIS' => 'Kommunalwahl - Kreis',
        'KW_REG_BEZIRK' => 'Kommunalwahl - Regierungsbezirk',
        'KW_VERBAND' => 'Kommunalwahl - Verband',
    ];

    public function getDescription(): string
    {
        return 'Add Wahlkreistype data';
    }

    public function up(Schema $schema): void
    {
        foreach (self::DATA as $abbr => $name) {
            $this->addSql(
                \sprintf('INSERT INTO wahlkreistyp (abbreviation, name) VALUES (\'%s\', \'%s\')', $abbr, $name),
            );
        }
    }

    public function down(Schema $schema): void
    {
        foreach (self::DATA as $name) {
            $this->addSql(
                \sprintf('DELETE FROM  wahlkreistyp  WHERE name = \'%s\'', $name),
            );
        }
    }
}
