<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\SupportNumbers;

final readonly class SupportNumbersItem
{
    public function __construct(
        public int $approved,
        public int $unapproved,
    ) {
    }
}
