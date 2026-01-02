<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Unit\Twig\Extension;

use App\Tracking\Matomo;
use App\Twig\Extension\TrackingExtension;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\HttpFoundation\Request;

class TrackingExtensionTest extends TestCase
{
    use ProphecyTrait;

    public function testGetFunctions(): void
    {
        $matomo = $this->prophesize(Matomo::class);
        $matomo->track(Argument::type(Request::class), Argument::type('string'))->shouldBeCalled();
        $extension = new TrackingExtension($matomo->reveal());
        $extension->tracking(new Request(), 'foo');
    }
}
