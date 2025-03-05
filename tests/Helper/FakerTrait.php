<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Helper;

use App\Tests\Faker\Bundeslaender;
use Faker\Factory;
use Faker\Generator;

trait FakerTrait
{
    protected Generator $faker;

    protected function loadFaker(): void
    {
        $this->faker = Factory::create('de_DE');
        $this->faker->addProvider(new Bundeslaender($this->faker));
    }

    protected static function createFaker(): Generator
    {
        $faker = Factory::create('de_DE');
        $faker->addProvider(new Bundeslaender($faker));

        return $faker;
    }
}
