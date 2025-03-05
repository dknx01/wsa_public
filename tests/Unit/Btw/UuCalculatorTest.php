<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Unit\Btw;

use App\Btw\UuCalculator;
use App\SupportNumbers\Dvo\StateItem;
use App\SupportNumbers\SupportNumbersItem;
use App\SupportNumbers\Type;
use App\Tests\Builder\Entity\WahlkreisBuilder;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class UuCalculatorTest extends TestCase
{
    public static function provideUUsNoSecurity(): \Generator
    {
        yield [
            0,
            0,
            new StateItem('Berlin', 'LL Berlin', new SupportNumbersItem(50, 10), 'Landesliste', null),
        ];
        yield [
            5,
            100,
            new StateItem('Berlin', 'LL Berlin', new SupportNumbersItem(50, 10), Type::LL_BTW->value, null),
        ];
        yield [
            21,
            100,
            new StateItem('Bremen', 'LL Bremen', new SupportNumbersItem(50, 10), Type::LL_BTW->value, null),
        ];
        yield [
            20,
            100,
            new StateItem('Bremen', 'Direktkandidat Foo', new SupportNumbersItem(50, 10), Type::DK_KW->value, WahlkreisBuilder::build()->setThreshold(500)),
        ];
    }

    #[DataProvider('provideUUsNoSecurity')]
    public function testCalculatePercentage(int $expected, int $uu, StateItem $stateItem): void
    {
        $uuCalculator = new UuCalculator();
        $this->assertEquals($expected, $uuCalculator->calculatePercentage($uu, $stateItem));
    }
}
