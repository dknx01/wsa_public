<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Btw;

use App\SupportNumbers\Dvo\StateItem;
use App\SupportNumbers\Type;

class UuCalculator
{
    public function calculatePercentage(int $uu, StateItem $item): int
    {
        return match ($item->type) {
            Type::LL_BTW->value,'Landesliste' => (int) floor(($uu * 100) / Wahlkreise::UU_NUMBERS[$item->state]),
            default => (int) floor(($uu * 100) / $item->wahlkreis->getThreshold()),
        };
    }
}
