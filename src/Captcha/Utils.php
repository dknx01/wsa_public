<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Captcha;

use function Symfony\Component\String\u;

class Utils
{
    /**
     * @param string|int[] $color
     *
     * @return array<array-key, float|int>
     *
     * @throws \Exception
     */
    public static function hex2rgb(string|array $color): array
    {
        if (\is_array($color)) {
            if (3 !== \count($color)) {
                throw new \Exception(\sprintf('Invalid RGB colors: "%s"', u()->join($color)));
            }

            return $color;
        }
        $hex = str_replace('#', '', $color);
        $length = \strlen($hex);

        if (3 === $length && preg_match('/^[a-f0-9]{3}$/i', $hex)) {
            return [
                hexdec(str_repeat($hex[0], 2)),
                hexdec(str_repeat($hex[1], 2)),
                hexdec(str_repeat($hex[2], 2)),
            ];
        }

        if (6 === $length && preg_match('/^[a-f0-9]{6}$/i', $hex)) {
            return [
                hexdec(substr($hex, 0, 2)),
                hexdec(substr($hex, 2, 2)),
                hexdec(substr($hex, 4, 2)),
            ];
        }

        throw new \Exception(\sprintf('Invalid HEX color: "%s"', $color));
    }
}
