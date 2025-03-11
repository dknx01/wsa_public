<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Unit\Tracking;

use App\Tracking\Matomo;
use App\Tracking\MatomoConfig;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\HttpFoundation\Request;

class MatomoTest extends TestCase
{
    use ProphecyTrait;

    public function testTrackForNotEnabled(): void
    {
        $matomoTracker = $this->prophesize(\MatomoTracker::class);
        $matomoTracker->doTrackPageView(Argument::type('string'))->shouldNotBeCalled();
        $config = new MatomoConfig(
            false,
            'www.claudine-kuhic.test',
            'https://example.test',
            'matomo.php',
            '89cr23',
            'https://example.test/matomo.php?img=68348244342',
            true
        );
        $matomo = new Matomo($matomoTracker->reveal(), $config);
        $matomo->track(new Request(), 'test');
    }

    public function testTrackEnabled(): void
    {
        $request = new Request(server: [
            'REMOTE_ADDR' => '127.0.0.1',
            'HTTP_USER_AGENT' => 'Foo',
            'HTTP_ACCEPT_LANGUAGE' => 'de-DE',
        ]);
        $matomoTracker = $this->prophesize(\MatomoTracker::class);
        $matomoTracker->setIp('127.0.0.1')->shouldBeCalled();
        $matomoTracker->setUserAgent('Foo')->shouldBeCalled();
        $matomoTracker->setBrowserLanguage('de-DE')->shouldBeCalled();
        $matomoTracker->setUrl('http://:/')->shouldBeCalled();
        $matomoTracker->setUrlReferrer(Argument::any())->shouldBeCalled();
        $matomoTracker->doTrackPageView(Argument::type('string'))->shouldBeCalled();
        $config = new MatomoConfig(
            true,
            'www.claudine-kuhic.test',
            'https://example.test',
            'matomo.php',
            '89cr23',
            'https://example.test/matomo.php?img=68348244342',
            true
        );
        $matomo = new Matomo($matomoTracker->reveal(), $config);
        $matomo->track($request, 'test');
    }
}
