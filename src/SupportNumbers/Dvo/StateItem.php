<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\SupportNumbers\Dvo;

use App\Entity\Wahlkreis;
use App\SupportNumbers\SupportNumbersItem;

final readonly class StateItem
{
    public function __construct(
        public string $state,
        public string $name,
        public SupportNumbersItem $item,
        public string $type,
        public ?Wahlkreis $wahlkreis,
    ) {
    }
}
