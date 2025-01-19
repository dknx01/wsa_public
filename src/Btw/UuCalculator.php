<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Btw;

class UuCalculator
{
    public const int SECURITY = 10;

    public function calculatePercentage(int $uu, string $state): int
    {
        return (int) floor(($uu * 100) / Wahlkreise::UU_NUMBERS[$state]);
    }
}
