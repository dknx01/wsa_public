<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Unit\Captcha;

use App\Captcha\Utils;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class UtilsTest extends TestCase
{
    public function testHex2rgbWithException(): void
    {
        $this->expectExceptionMessageMatches('~^Invalid\ RGB\ colors.+$~i');
        Utils::hex2rgb([]);
    }

    #[TestWith(['#12'])]
    #[TestWith(['#12av'])]
    public function testHex2rgbWithExceptionStringColor(string $color): void
    {
        $this->expectExceptionMessageMatches('~^Invalid\ Hex\ color.+$~i');
        Utils::hex2rgb($color);
    }

    public static function provideColors(): \Generator
    {
        yield [
            [255, 155, 55],
            [255, 155, 55],
        ];
        yield [
            [170, 187, 34],
            '#ab2',
        ];
        yield [
            [171, 253, 21],
            '#abfd15',
        ];
    }

    #[DataProvider('provideColors')]
    public function testHex2Rgb(array $expected, string|array $color): void
    {
        $this->assertEquals($expected, Utils::hex2rgb($color));
    }
}
