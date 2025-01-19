<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Dto\Api;

readonly class DocumentResponse
{
    public function __construct(
        public string $name,
        public string $image,
        public string $description,
        public string $url,
        public string $state,
    ) {
    }
}
