<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Unit\EventListener;

use App\EventListener\AccessDeniedListener;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Twig\Environment;

class AccessDeniedListenerTest extends TestCase
{
    use ProphecyTrait;

    public function testGetSubscribedEvents(): void
    {
        $this->assertSame(
            [KernelEvents::EXCEPTION => ['onKernelException', 2]],
            AccessDeniedListener::getSubscribedEvents()
        );
    }

    public function testOnKernelExceptionWithNonAccessDeniedException(): void
    {
        $twig = $this->prophesize(Environment::class);
        $listener = new AccessDeniedListener($twig->reveal());
        $request = Request::create('/');
        $event = new ExceptionEvent(
            $this->prophesize(KernelInterface::class)->reveal(),
            $request,
            0,
            new \Exception()
        );
        $event->setResponse(new Response());
        $listener->onKernelException($event);
        $this->assertEmpty($event->getResponse()->getContent());
    }

    public function testOnKernelExceptionWithAccessDeniedException(): void
    {
        $twig = $this->createMock(Environment::class);
        $twig->expects($this->once())->method('render')->willReturn('no');

        $listener = new AccessDeniedListener($twig);
        $request = Request::create('/');
        $event = new ExceptionEvent(
            $this->prophesize(KernelInterface::class)->reveal(),
            $request,
            0,
            new AccessDeniedException()
        );
        $event->setResponse(new Response());
        $listener->onKernelException($event);
        $this->assertSame('no', $event->getResponse()->getContent());
    }
}
