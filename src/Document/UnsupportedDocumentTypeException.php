<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Document;

class UnsupportedDocumentTypeException extends \Exception
{
    public function __construct(string $type)
    {
        parent::__construct(\sprintf('Unsupported document type: %s', $type));
    }
}
