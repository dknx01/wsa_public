<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Unit\Security;

use App\Security\AuthenticationEntryPoint;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AuthenticationEntryPointTest extends TestCase
{
    public function testStart(): void
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->expects($this->once())->method('generate')->willReturn('/');
        $authenticationEntryPoint = new AuthenticationEntryPoint($urlGenerator);
        $request = Request::create('');
        $request->setSession(new Session(new MockArraySessionStorage()));
        $response = $authenticationEntryPoint->start($request);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(
            [
                'Diese Seite erfordert einen Login.',
            ], $request->getSession()->getBag('flashes')->get('info'));
    }
}
