<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Unit\Captcha;

use App\Captcha\Configuration;
use App\Captcha\Text;
use PHPUnit\Framework\TestCase;

use function Symfony\Component\String\u;

class TextTest extends TestCase
{
    public function testWrite(): void
    {
        $configuration = new Configuration(u('B'));
        $configuration->image = imagecreatetruecolor($configuration::WIDTH, $configuration::HEIGHT);
        $text = new Text($configuration, \dirname(__DIR__, 3).'/data/JetBrainsMono-ExtraBold.ttf');
        $text->write();
        $this->expectNotToPerformAssertions();
    }

    public function testGenerateText(): void
    {
        $configuration = new Configuration(u());

        $text = new Text($configuration, \dirname(__DIR__, 3).'/data/JetBrainsMono-ExtraBold.ttf');
        $text->generateText();

        $this->assertFalse($configuration->captchaCode->isEmpty());
    }
}
