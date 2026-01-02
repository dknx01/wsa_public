<?php

declare(strict_types=1);

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Twig\Extension;

use App\Entity\User;
use Twig\Attribute\AsTwigFunction;

class AdminExtension
{
    #[AsTwigFunction('displayString')]
    public function displayString(?string $value): string
    {
        return $value ?? '';
    }

    #[AsTwigFunction('displayUserEmail')]
    public function displayUserEmail(?User $value): string
    {
        return $value ? $this->displayString($value->getEmail()) : '';
    }
}
