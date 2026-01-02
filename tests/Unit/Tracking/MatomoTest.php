<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Unit\Tracking;

use App\Tests\Helper\FakerTrait;
use App\Tracking\Matomo;
use App\Tracking\MatomoConfig;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\Request;

class MatomoTest extends TestCase
{
    use FakerTrait;
    use ProphecyTrait;

    public static function provideConfig(): \Generator
    {
        $faker = self::createFaker();
        yield [
            new Request(),
            new MatomoConfig(false, $faker->domainName(), 'matomo.test', $faker->url(), '1', '', true),
            function (\MatomoTracker|ObjectProphecy $tracker) {
                $tracker->setIp(Argument::type('string'))->shouldNotBeCalled();
            },
        ];
        yield [
            new Request(
                server: [
                    'REMOTE_ADDR' => '127.0.0.1',
                    'HTTP_User-Agent' => $faker->userAgent(),
                    'HTTP_Accept-Language' => 'de_DE',
                ],
            ),
            new MatomoConfig(true, $faker->domainName(), 'matomo.test', $faker->url(), '1', '', true),
            function (\MatomoTracker|ObjectProphecy $tracker) {
                $tracker->setIp('127.0.0.1')->shouldBeCalledOnce();
                $tracker->setUrl('http://:/')->shouldBeCalledOnce();
                $tracker->setUserAgent(Argument::that(fn (string $value) => !empty($value)))->shouldBeCalledOnce();
                $tracker->setBrowserLanguage('de_DE')->shouldbeCalledOnce();
                $tracker->setUrlReferrer(Argument::type('string'))->shouldbeCalledOnce();
                $tracker->doTrackPageView('TestPage')->shouldBeCalledOnce();
            },
        ];
        yield [
            new Request(
                server: [
                    'REMOTE_ADDR' => '127.0.0.1',
                    'HTTP_User-Agent' => $faker->userAgent(),
                    'HTTP_Accept-Language' => 'de_DE',
                    'HTTP_Referer' => 'http://referer.test',
                ],
            ),
            new MatomoConfig(true, $faker->domainName(), 'matomo.test', $faker->url(), '1', '', true),
            function (\MatomoTracker|ObjectProphecy $tracker) {
                $tracker->setIp('127.0.0.1')->shouldBeCalledOnce();
                $tracker->setUrl('http://:/')->shouldBeCalledOnce();
                $tracker->setUserAgent(Argument::that(fn (string $value) => !empty($value)))->shouldBeCalledOnce();
                $tracker->setBrowserLanguage('de_DE')->shouldbeCalledOnce();
                $tracker->setUrlReferrer('http://referer.test')->shouldbeCalledOnce();
                $tracker->doTrackPageView('TestPage')->shouldBeCalledOnce();
            },
        ];
    }

    #[DataProvider('provideConfig')]
    public function testTrack(Request $request, MatomoConfig $config, callable $trackerCallback): void
    {
        $tracker = $this->prophesize(\MatomoTracker::class);
        $trackerCallback($tracker);
        $matomo = new Matomo($tracker->reveal(), $config);
        $this->assertIsBool($config->enabled);
        $this->assertIsString($config->cookieDomain);
        $this->assertIsString($config->matomoDomain);
        $this->assertIsString($config->trackerUrl);
        $this->assertIsString($config->siteId);
        $this->assertIsString($config->noScriptImage);
        $this->assertIsBool($config->serverSide);

        $matomo->track($request, 'TestPage');
    }
}
