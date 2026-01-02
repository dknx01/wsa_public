<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Twig\Extension;

use App\Configuration\Configuration;
use Twig\Attribute\AsTwigFunction;

readonly class ConfigurationExtension
{
    public function __construct(private Configuration $configuration)
    {
    }

    #[AsTwigFunction('getImpress')]
    public function impress(): string
    {
        return $this->configuration->getImpress();
    }

    #[AsTwigFunction('organisationLogo')]
    public function organisationLogo(): string
    {
        return $this->configuration->getOrganisationLogo();
    }

    #[AsTwigFunction('pageLogo')]
    public function logo(): string
    {
        return $this->configuration->getLogo();
    }

    #[AsTwigFunction('pageTitle')]
    public function title(): string
    {
        return $this->configuration->getPageTitle();
    }

    #[AsTwigFunction('uuHelpImage')]
    public function uuHelpImage(string $type): string
    {
        return match ($type) {
            'wk' => $this->configuration->getUuWkAsBase64(),
            'll' => $this->configuration->getUuLlAsBase64(),
            default => 'data:image/png;base64,',
        };
    }
}
