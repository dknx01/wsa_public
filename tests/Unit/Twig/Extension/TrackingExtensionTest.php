<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Unit\Twig\Extension;

use App\Tracking\Matomo;
use App\Twig\Extension\TrackingExtension;
use PHPUnit\Framework\TestCase;

class TrackingExtensionTest extends TestCase
{
    public function testGetFunctions(): void
    {
        $extension = new TrackingExtension();
        $functions = $extension->getFunctions();
        $this->assertCount(1, $functions);
        $this->assertSame('tracking', $functions[0]->getName());
        $this->assertEquals([Matomo::class, 'track'], $functions[0]->getCallable());
    }
}
