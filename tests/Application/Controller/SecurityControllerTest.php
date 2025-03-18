<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Application\Controller;

use App\Controller\SecurityController;
use App\Tests\Application\ApplicationTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SecurityControllerTest extends ApplicationTestCase
{
    public function testLogin(): void
    {
        $this->startApplication();
        $this->initDatabase(static::$kernel);

        $this->client->request('GET', $this->getRouter()->generate(name: 'app_login', referenceType: UrlGeneratorInterface::RELATIVE_PATH));

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Bitte Einloggen');
    }

    public function testLogout(): void
    {
        $this->startApplication();
        $this->initDatabase(self::$kernel);

        $this->client->request('GET', $this->getRouter()->generate(name: 'app_logout', referenceType: UrlGeneratorInterface::RELATIVE_PATH));
        self::assertResponseRedirects($this->getRouter()->generate(name: 'home', referenceType: UrlGeneratorInterface::NETWORK_PATH));

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('This method can be blank - it will be intercepted by the logout key on your firewall.');
        (new SecurityController())->logout();
    }
}
