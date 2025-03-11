<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tracking;

use Symfony\Component\HttpFoundation\Request;
use Twig\Extension\RuntimeExtensionInterface;

readonly class Matomo implements RuntimeExtensionInterface
{
    public function __construct(private \MatomoTracker $tracker, private MatomoConfig $config)
    {
    }

    public function track(Request $request, string $pageTitle): void
    {
        if (!$this->config->enabled) {
            return;
        }
        $this->tracker->setIp($request->getClientIp());
        $this->tracker->setUrl($request->getUri());
        $this->tracker->setUserAgent($request->headers->get('User-Agent'));
        $this->tracker->setBrowserLanguage($request->headers->get('Accept-Language'));
        $this->tracker->setUrlReferrer($request->headers->get('Referer', ''));
        $this->tracker->doTrackPageView($pageTitle);
    }
}
