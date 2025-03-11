<?php

declare(strict_types=1);

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Twig\Extension;

use App\Tracking\Matomo;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TrackingExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('tracking', [Matomo::class, 'track']),
        ];
    }
}
