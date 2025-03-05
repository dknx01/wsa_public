<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Statistic;

use App\Entity\Statistic;

readonly class OverviewItem
{
    public function __construct(
        public Statistic $statistic,
        public bool $hasAccess = false,
    ) {
    }
}
