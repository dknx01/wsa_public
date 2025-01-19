<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Unit\EventListener;

use App\EventListener\LoginListener;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class LoginListenerTest extends TestCase
{
    use ProphecyTrait;

    public function testOnLoginSuccess(): void
    {
        $router = $this->createMock(RouterInterface::class);
        $router->expects($this->once())
            ->method('generate')
            ->with('admin')
            ->willReturn('/admin');
        $listener = new LoginListener($router);
        $authenticator = $this->prophesize(AuthenticatorInterface::class);
        $passport = $this->prophesize(Passport::class);
        $authenticatedToken = $this->prophesize(TokenInterface::class);
        $request = Request::create('');
        $response = new Response();
        $event = new LoginSuccessEvent(
            $authenticator->reveal(),
            $passport->reveal(),
            $authenticatedToken->reveal(),
            $request,
            $response,
            'main'
        );
        $listener->onLoginSuccess(
            $event
        );

        $this->assertInstanceOf(RedirectResponse::class, $event->getResponse());
    }

    public function testGetSubscribedEvents(): void
    {
        $this->assertSame(
            [
                LoginSuccessEvent::class => 'onLoginSuccess',
            ],
            LoginListener::getSubscribedEvents()
        );
    }
}
