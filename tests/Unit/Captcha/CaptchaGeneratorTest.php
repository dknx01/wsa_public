<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Unit\Captcha;

use App\Captcha\CaptchaGenerator;
use PHPUnit\Framework\TestCase;

class CaptchaGeneratorTest extends TestCase
{
    private function getGenerator(): CaptchaGenerator
    {
        return new CaptchaGenerator(\dirname(__DIR__, 3).'/data/JetBrainsMono-ExtraBold.ttf');
    }

    public function testGenerateCaptcha(): void
    {
        $this->assertNotEmpty($this->getGenerator()->generateCaptcha());
    }

    public function testGetCaptchaCode(): void
    {
        $generator = $this->getGenerator();
        $generator->generateCaptcha();
        $this->assertNotEmpty($generator->getCaptchaCode());
    }
}
