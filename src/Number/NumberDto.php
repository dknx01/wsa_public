<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Number;

use App\Entity\Wahlkreis;

class NumberDto
{
    public string $bundesland = '';
    public ?string $approved = null;
    public ?string $unapproved = null;
    public ?string $comment = null;
    public string $name = '';
    public ?Wahlkreis $wahlkreis = null;
    public ?\DateTimeInterface $updatedAt = null;
}
