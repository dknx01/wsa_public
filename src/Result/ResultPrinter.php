<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Result;

use App\Configuration\Configuration;
use JsonSchema\Validator;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

readonly class ResultPrinter
{
    private const string CACHE_KEY = 'result';

    public function __construct(
        private CacheInterface $cache,
        private Configuration $configuration,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     * @throws \JsonException
     */
    public function getResults(): \Generator
    {
        /** @var ResultItem $result */
        foreach ($this->cache->get(self::CACHE_KEY, fn (ItemInterface $item, bool $save) => $this->generateResults()) as $result) {
            yield $result->asArray();
        }
    }

    /**
     * @return ResultItem[]
     *
     * @throws \JsonException
     */
    private function generateResults(): array
    {
        $validator = new Validator();

        $json = json_decode(file_get_contents($this->configuration->getResultFile()), false, 512, \JSON_THROW_ON_ERROR);
        $validator->validate($json, (object) ['$ref' => 'file://'.realpath(__DIR__.'/result.schema.json')]);
        if (!$validator->isValid()) {
            throw new \RuntimeException('Cannot parse result file as it is not a valid JSON');
        }
        $json = json_decode(file_get_contents($this->configuration->getResultFile()), true, 512, \JSON_THROW_ON_ERROR);
        $data = [];
        foreach ($json as $state => $item) {
            $data[] = new ResultItem(
                $state,
                $item['LL'],
                $item['WKs'] ?? [],
                $item['Kommunal'] ?? [],
            );
        }

        return $data;
    }
}
