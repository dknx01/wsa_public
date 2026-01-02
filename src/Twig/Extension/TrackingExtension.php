<?php

declare(strict_types=1);

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Twig\Extension;

use App\Tracking\Matomo;
use Symfony\Component\HttpFoundation\Request;
use Twig\Attribute\AsTwigFunction;

readonly class TrackingExtension
{
    public function __construct(private Matomo $matomo)
    {
    }

    #[AsTwigFunction('tracking')]
    public function tracking(Request $request, string $title): void
    {
        $this->matomo->track($request, $title);
    }
}
