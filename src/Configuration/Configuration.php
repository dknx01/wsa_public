<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Configuration;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

use function Symfony\Component\String\u;

class Configuration
{
    private ConfigurationItem $config;

    public function __construct(
        private readonly CacheInterface $cache,
        #[Autowire('%kernel.project_dir%/wsa.config.php')] string $configPath,
        #[Autowire('%kernel.project_dir%')] string $basePath,
    ) {
        $config = require $configPath;
        $config['impress'] = u($config['impress'])->replace('\n', '')->toString();
        $config['logoPath'] = $this->cleanupNonStream($basePath.'/'.$config['logoPath']);
        $config['pageLogoPath'] = $this->cleanupNonStream($basePath.'/'.$config['pageLogoPath']);
        $config['uuHelpWk'] = $this->cleanupNonStream($basePath.'/'.$config['uuHelpWk']);
        $config['uuHelpLl'] = $this->cleanupNonStream($basePath.'/'.$config['uuHelpLl']);
        $config['resultFile'] = $this->cleanupNonStream($basePath.'/'.$config['resultFile']);
        $this->config = $this->cache->get(
            'wsa_configuration',
            fn (ItemInterface $item, bool $save) => new ConfigurationItem(...$config)
        );
    }

    public function getImpress(): string
    {
        return $this->config->impress;
    }

    public function getOrganisationLogo(): string
    {
        return (string) file_get_contents($this->config->logoPath);
    }

    public function getLogo(): string
    {
        return (string) file_get_contents($this->config->pageLogoPath);
    }

    public function getPageTitle(): string
    {
        return $this->config->pageTitle;
    }

    public function getUuWkAsBase64(): string
    {
        return \sprintf('data:%s;base64,%s', 'image/png', base64_encode((string) file_get_contents($this->config->uuHelpWk)));
    }

    public function getUuLLAsBase64(): string
    {
        return \sprintf('data:%s;base64,%s', 'image/png', base64_encode((string) file_get_contents($this->config->uuHelpLl)));
    }

    public function getResultFile(): string
    {
        return $this->config->resultFile;
    }

    public function getUuHelp(string $type): string
    {
        return match ($type) {
            'wk' => $this->getUuWkAsBase64(),
            'll' => $this->getUuLlAsBase64(),
            default => 'data:image/png;base64,',
        };
    }

    private function cleanupNonStream(string $string): string
    {
        return preg_match('#^[a-z]+://.+$#', $string) ? $string : realpath($string);
    }
}
