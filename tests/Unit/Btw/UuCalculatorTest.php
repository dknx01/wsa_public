<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Unit\Btw;

use App\Btw\UuCalculator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class UuCalculatorTest extends TestCase
{
    public static function provideUUsNoSecurity(): \Generator
    {
        yield [0, 0, 'Berlin'];
        yield [5, 100, 'Berlin'];
        yield [21, 100, 'Bremen'];
    }

    #[DataProvider('provideUUsNoSecurity')]
    public function testCalculatePercentage(int $expected, int $uu, string $state): void
    {
        $uuCalculator = new UuCalculator();
        $this->assertEquals($expected, $uuCalculator->calculatePercentage($uu, $state));
    }
}
