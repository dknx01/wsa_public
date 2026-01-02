<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Dto\Document;

use App\Entity\Wahlkreis;

class WahlkreisDto
{
    public ?Wahlkreis $wahlkreis = null;

    public static function new(?Wahlkreis $wahlkreis = null): self
    {
        $class = new self();
        $class->wahlkreis = $wahlkreis;

        return $class;
    }
}
