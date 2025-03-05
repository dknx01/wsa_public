<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Dto;

readonly class Statistic
{
    public function __construct(
        public string $name,
        public int $approved,
        public int $approvedPercentage,
        public int $unapproved,
        public int $total,
        public int $unapprovedPercentage,
        public string $colors,
        public string $unapprovedColors,
        public int $max,
        public string $status,
        public string $type,
    ) {
    }
}
