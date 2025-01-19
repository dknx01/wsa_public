<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Unit\Dto;

use App\Dto\UuListe;
use App\Entity\Wahlkreis;
use App\Tests\Builder\Entity\WahlkreisBuilder;
use PHPUnit\Framework\TestCase;

class UuListeTest extends TestCase
{
    public function testSetWahlkreisLink(): void
    {
        $wahlkreise = $this->getWahlkreise();
        $uuListe = new UuListe($wahlkreise);
        $link = 'http:// localhost/foo/bar';
        $wahlkreis = $wahlkreise[0]->getName().' (Nr.999)';

        $uuListe->setWahlkreisLink($wahlkreis, $link);
        $firstKey = array_key_first($uuListe->wahlkreise);
        $this->assertNotEmpty($uuListe->wahlkreise[$firstKey]['link']);
    }

    public function testGetWahlkreisNumbers(): void
    {
        $uuListe = new UuListe($this->getWahlkreise());
        $this->assertMatchesRegularExpression(
            '#^[0-9]+ - [0-9]+$#',
            $uuListe->getWahlkreisNumbers()
        );
    }

    /**
     * @return Wahlkreis[]
     */
    private function getWahlkreise(): array
    {
        return [
            WahlkreisBuilder::build(),
            WahlkreisBuilder::build(),
        ];
    }
}
