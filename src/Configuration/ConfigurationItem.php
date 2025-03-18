<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Configuration;

final readonly class ConfigurationItem
{
    /**
     * @param string[]|null $resultAsStart
     */
    public function __construct(
        public string $pageTitle,
        public string $impress,
        public string $logoPath,
        public string $pageLogoPath,
        public string $uuHelpWk,
        public string $uuHelpLl,
        public string $resultFile,
        public string $privacy,
        public ?array $resultAsStart,
    ) {
    }
}
