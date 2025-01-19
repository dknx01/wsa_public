<?php

declare(strict_types=1);

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241128211413 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Insert Verbandsseiten data';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<SQL
        INSERT INTO verbandsseiten (id, name, link, link_name) VALUES (1, 'Schleswig-Holstein', 'https://die-partei.sh/sh/btw25-unterschriften/', 'die-partei.sh');
INSERT INTO verbandsseiten (id, name, link, link_name) VALUES (2, 'Mecklenburg-Vorpommern', null, null);
INSERT INTO verbandsseiten (id, name, link, link_name) VALUES (3, 'Hamburg', null, null);
INSERT INTO verbandsseiten (id, name, link, link_name) VALUES (4, 'Niedersachsen', 'https://partei-nds.de/', 'partei-nds.de');
INSERT INTO verbandsseiten (id, name, link, link_name) VALUES (5, 'Bremen', null, null);
INSERT INTO verbandsseiten (id, name, link, link_name) VALUES (6, 'Brandenburg', null, null);
INSERT INTO verbandsseiten (id, name, link, link_name) VALUES (7, 'Sachsen-Anhalt', null, null);
INSERT INTO verbandsseiten (id, name, link, link_name) VALUES (8, 'Berlin', null, null);
INSERT INTO verbandsseiten (id, name, link, link_name) VALUES (9, 'Nordrhein-Westfalen', null, null);
INSERT INTO verbandsseiten (id, name, link, link_name) VALUES (10, 'Sachsen', null, null);
INSERT INTO verbandsseiten (id, name, link, link_name) VALUES (11, 'Hessen', null, null);
INSERT INTO verbandsseiten (id, name, link, link_name) VALUES (12, 'Rheinland-Pfalz', null, null);
INSERT INTO verbandsseiten (id, name, link, link_name) VALUES (13, 'Bayern', null, null);
INSERT INTO verbandsseiten (id, name, link, link_name) VALUES (14, 'Baden-Württemberg', 'https://www.die-partei-bw.de/', 'www.die-partei-bw.de');
INSERT INTO verbandsseiten (id, name, link, link_name) VALUES (15, 'Saarland', null, null);
INSERT INTO verbandsseiten (id, name, link, link_name) VALUES (16, 'Thüringen', null, null);
SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('TRUNCATE verbandsseiten');
    }
}
