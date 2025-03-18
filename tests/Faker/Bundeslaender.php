<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Faker;

use Faker\Provider\Base;

class Bundeslaender extends Base
{
    private const array STATES = [
        'Baden-Württemberg',
        'Bayern',
        'Berlin',
        'Brandenburg',
        'Bremen',
        'Hamburg',
        'Hessen',
        'Mecklenburg-Vorpommern',
        'Niedersachsen',
        'Nordrhein-Westfalen',
        'Rheinland-Pfalz',
        'Saarland',
        'Sachsen',
        'Sachsen-Anhalt',
        'Schleswig-Holstein',
        'Thüringen',
    ];

    public function bundesland(): string
    {
        return self::randomElement(self::STATES);
    }

    /**
     * @param string|string[] $exclude
     */
    public function bundeslandExclude(string|array $exclude): string
    {
        if (!\is_array($exclude)) {
            $exclude = [$exclude];
        }
        $state = self::randomElement(self::STATES);

        while (\in_array($state, $exclude, true)) {
            $state = self::randomElement(self::STATES);
        }

        return $state;
    }
}
