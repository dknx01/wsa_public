<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Twig\Extension;

use App\Configuration\Configuration;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ConfigurationExtension extends AbstractExtension
{
    public function __construct(private readonly Configuration $configuration)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getImpress', fn () => $this->configuration->getImpress()),
            new TwigFunction('organisationLogo', fn () => $this->configuration->getOrganisationLogo()),
            new TwigFunction('pageLogo', fn () => $this->configuration->getLogo()),
            new TwigFunction('pageTitle', fn () => $this->configuration->getPageTitle()),
            new TwigFunction('uuHelpImage', fn (string $type) => match ($type) {
                'wk' => $this->configuration->getUuWkAsBase64(),
                'll' => $this->configuration->getUuLlAsBase64(),
                default => 'data:image/png;base64,',
            }
            ),
        ];
    }
}
