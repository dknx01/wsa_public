<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tracking;

readonly class MatomoConfig
{
    public function __construct(
        public bool $enabled,
        public string $cookieDomain,
        public string $matomoDomain,
        public string $trackerUrl,
        public string $siteId,
        public string $noScriptImage,
        public bool $serverSide,
    ) {
    }
}
