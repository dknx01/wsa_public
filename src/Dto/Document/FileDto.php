<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Dto\Document;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileDto
{
    public ?string $type = null;
    public ?UploadedFile $file = null;
    public ?string $description = null;

    public static function new(?string $type = null, ?UploadedFile $file = null, ?string $description = null): self
    {
        $class = new self();
        $class->type = $type;
        $class->file = $file;
        $class->description = $description;

        return $class;
    }
}
