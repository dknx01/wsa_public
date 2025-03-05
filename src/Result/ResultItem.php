<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Result;

final readonly class ResultItem
{
    /**
     * @param array<array-key, array{'name': string, 'uus': ?int, 'zugelassen': ?int}> $wahlkreise
     * @param array<array-key, array{'name': string, 'uus': ?int, 'zugelassen': ?int}> $kommunal
     */
    public function __construct(
        public string $state,
        public string $landesListe,
        public array $wahlkreise,
        public array $kommunal,
    ) {
    }

    /**
     * @return array{
     *     state: string,
     *     LL: string,
     *     WKs: list<non-empty-list<array{name: string, uus: int|null, zugelassen: int|null}>>,
     *     Kommunal: list<non-empty-list<array{name: string, uus: int|null, zugelassen: int|null}>>
     *     }
     */
    public function asArray(): array
    {
        return [
            'state' => $this->state,
            'LL' => $this->landesListe,
            'WKs' => array_chunk($this->wahlkreise, (int) ceil(\count($this->wahlkreise) / 2)),
            'Kommunal' => array_chunk($this->kommunal, (int) ceil(\count($this->wahlkreise) / 2)),
        ];
    }
}
