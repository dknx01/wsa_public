<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Dto\Document;

use Symfony\Component\Validator\Constraints as Assert;

class Document
{
    #[Assert\Valid(groups: ['wahlkreis'])]
    public ?WahlkreisDto $wahlkreis = null;
    public ?FileDto $file = null;
    public string $currentStep = 'wahlkreis';

    public static function new(?WahlkreisDto $wahlkreis = null, ?FileDto $file = null, string $currentStep = 'wahlkreis'): self
    {
        $class = new self();
        $class->currentStep = $currentStep;
        if ($wahlkreis) {
            $class->wahlkreis = $wahlkreis;
        }
        if ($file) {
            $class->file = $file;
        }

        return $class;
    }
}
