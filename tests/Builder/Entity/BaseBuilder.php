<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Builder\Entity;

use App\Tests\Helper\FakerTrait;
use Faker\Generator;

abstract class BaseBuilder
{
    use FakerTrait;
    private static ?Generator $fakerInstance = null;

    protected static function faker(): Generator
    {
        if (null === self::$fakerInstance) {
            self::$fakerInstance = self::createFaker();
        }

        return self::$fakerInstance;
    }
}
