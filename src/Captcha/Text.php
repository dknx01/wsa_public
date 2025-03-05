<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Captcha;

use Random\RandomException;

readonly class Text
{
    public function __construct(
        private Configuration $config,
        private string $fontPath,
    ) {
    }

    /**
     * @throws RandomException
     */
    public function generateText(): void
    {
        $count = 0;
        while ($count < $this->config::TOTAL_CHARACTERS_ON_IMAGE) {
            $char = $this->config::CHARACTERS[random_int(0, \strlen($this->config::CHARACTERS) - 1)];
            while ($this->config->captchaCode->containsAny($char)) {
                $char = $this->config::CHARACTERS[random_int(0, \strlen($this->config::CHARACTERS) - 1)];
            }
            $this->config->captchaCode = $this->config->captchaCode->append($char);
            ++$count;
        }
    }

    /**
     * @throws \Exception
     */
    public function write(): void
    {
        // Determine number of characters
        $length = $this->config->captchaCode->length();

        // Determine text size & start position
        $size = $this->round($this->config::WIDTH / $length) - random_int(0, 3) - 1;
        $box = imagettfbbox($size, 0, $this->fontPath, $this->config->captchaCode->toString());
        $textWidth = $box[2] - $box[0];
        $textHeight = $box[1] - $box[7];
        $x = $this->round(($this->config::WIDTH - $textWidth) / 2);
        $y = $this->round(($this->config::HEIGHT - $textHeight) / 2) + $size;

        for ($i = 0; $i < $length; ++$i) {
            // (b) Normalize RGB values
            $mix = Utils::hex2rgb($this->config::TEXT_COLOR);

            // (c) Mix them up
            $textCode = imagecolorallocate($this->config->image, $mix[0], $mix[1], $mix[2]);

            // Fetch current character & determine its width
            $char = $this->config->captchaCode->slice($i, 1);
            $box = imagettfbbox($size, 0, $this->fontPath, $char);
            $charWidth = $box[2] - $box[0];

            // Randomize angle & offset
            $angle = random_int(-$this->config::MAX_ANGLE, $this->config::MAX_ANGLE);
            $offset = random_int(-$this->config::MAX_OFFSET, $this->config::MAX_OFFSET);

            // Draw character
            imagettftext($this->config->image, $size, $angle, $x, $y + $offset, $textCode, $this->fontPath, $char);

            // Move along
            $x += $charWidth;
        }
    }

    private function round(float $number): int
    {
        return (int) round($number);
    }
}
