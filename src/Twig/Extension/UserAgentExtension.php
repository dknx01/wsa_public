<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Twig\Extension;

readonly class UserAgentExtension
{
    public function __construct(private string $filePath)
    {
    }

    public function __invoke(): \Generator
    {
        $already = [];
        $file = fopen($this->filePath, 'r');
        if ($file) {
            while (($userAgent = fgets($file)) !== false) {
                if (empty($userAgent)
                    || str_starts_with($userAgent, '#')
                    || \in_array($userAgent, $already, true)
                ) {
                    continue;
                }
                $already[] = $userAgent;
                yield $userAgent;
            }
            fclose($file);
        }
        yield '';
    }
}
