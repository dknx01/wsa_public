<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Dto\Api;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUpload
{
    public function __construct(
        public ?string $state = null,
        public ?string $type = null,
        public ?UploadedFile $file = null,
        public ?string $description = null,
    ) {
    }
}
