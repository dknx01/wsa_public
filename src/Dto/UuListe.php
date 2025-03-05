<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Dto;

use App\Entity\Wahlkreis;

use function Symfony\Component\String\u;

class UuListe
{
    public string $name;
    public ?string $sendTo = '';
    /**
     * @var array<int, array{
     *     'name': string,
     *     'link': string|null
     * }>
     */
    public array $wahlkreise = [];
    public ?string $link = null;
    public ?string $linkName = null;
    public ?string $linkLandesliste = null;

    /**
     * @param Wahlkreis[] $wahlkreise
     */
    public function __construct(array $wahlkreise)
    {
        ksort($wahlkreise);
        foreach ($wahlkreise as $wahlkreis) {
            $this->wahlkreise[$wahlkreis->getNumber()] = [
                'name' => $wahlkreis->getName(),
                'link' => null,
            ];
        }
    }

    public function getWahlkreisNumbers(): string
    {
        return \sprintf(
            '%s - %s',
            array_key_first($this->wahlkreise),
            array_key_last($this->wahlkreise));
    }

    public function setWahlkreisLink(string $wahlkreis, string $link): void
    {
        $wahlkreis = u($wahlkreis)->beforeLast('(Nr.')->trim()->toString();
        foreach ($this->wahlkreise as $k => $value) {
            if ($value['name'] === $wahlkreis) {
                $this->wahlkreise[$k]['link'] = $link;

                return;
            }
        }
    }
}
