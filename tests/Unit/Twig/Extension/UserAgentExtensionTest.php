<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Unit\Twig\Extension;

use App\Twig\Extension\UserAgentExtension;
use PHPUnit\Framework\TestCase;

class UserAgentExtensionTest extends TestCase
{
    public function testExtension(): void
    {
        $result = [];

        $sut = new UserAgentExtension(__DIR__.'/../../../../data/useragents.txt');

        foreach ($sut() as $value) {
            $result[] = $value;
        }
        $this->assertNotEmpty($result);
    }
}
