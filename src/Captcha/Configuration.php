<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Captcha;

use Symfony\Component\String\UnicodeString;

class Configuration
{
    public const string CHARACTERS = '23456789ABCDFGHLMNQRSTUVWXYZ';
    public const int MAX_ANGLE = 8;
    public const int MAX_OFFSET = 5;
    public const int HEIGHT = 50;
    public const int WIDTH = 130;
    public const int TOTAL_CHARACTERS_ON_IMAGE = 6;
    public const string TEXT_COLOR = '000000';
    public const string BACKGROUND_COLOR = 'c1bfc1';
    public const string CAPTCHA_NOISE_COLOR = '0x142864';
    public \GdImage $image;
    public ?int $maxLinesBehind = null;

    public function __construct(public UnicodeString $captchaCode)
    {
    }
}
