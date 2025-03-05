<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Captcha;

readonly class Line
{
    public function __construct(private Configuration $config)
    {
    }

    /**
     * @throws \Exception
     */
    public function draw(): void
    {
        // Determine direction at random, being either ..
        // (1) .. horizontal
        if (random_int(0, 1)) {
            $Xa = random_int(0, $this->config::WIDTH / 2);
            $Ya = random_int(0, $this->config::HEIGHT);
            $Xb = random_int($this->config::WIDTH / 2, $this->config::WIDTH);
            $Yb = random_int(0, $this->config::HEIGHT);

        // (2) .. vertical
        } else {
            $Xa = random_int(0, $this->config::WIDTH);
            $Ya = random_int(0, $this->config::HEIGHT / 2);
            $Xb = random_int(0, $this->config::WIDTH);
            $Yb = random_int($this->config::HEIGHT / 2, $this->config::HEIGHT);
        }

        $mix = $this->randomColor();

        // (3) Mix them up
        $color = imagecolorallocate($this->config->image, $mix[0], $mix[1], $mix[2]);

        // Randomize thickness & draw line
        imagesetthickness($this->config->image, 3);
        imageline($this->config->image, $Xa, $Ya, $Xb, $Yb, $color);
    }

    /**
     * @return array<array-key, float|int>
     *
     * @throws \Exception
     */
    private function randomColor(): array
    {
        $mix = [
            random_int(100, 255),  // red
            random_int(100, 255),  // green
            random_int(100, 255),  // blue
        ];

        return Utils::hex2rgb($mix);
    }
}
