<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Unit\Captcha;

use App\Captcha\Configuration;
use App\Captcha\Line;
use PHPUnit\Framework\TestCase;

use function Symfony\Component\String\u;

class LineTest extends TestCase
{
    public function testDraw(): void
    {
        $configuration = new Configuration(u());
        $configuration->image = imagecreatetruecolor($configuration::WIDTH, $configuration::HEIGHT);

        $line = new Line($configuration);
        for ($e = 0; $e < 100; ++$e) {
            $line->draw();
        }

        $this->assertInstanceOf(\GdImage::class, $configuration->image);
    }
}
