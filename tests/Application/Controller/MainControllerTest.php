<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Application\Controller;

use App\Tests\Helper\DatabaseInitTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MainControllerTest extends WebTestCase
{
    use DatabaseInitTrait;

    public function testInvoke(): void
    {
        $client = static::createClient();
        $this->initDatabase(static::$kernel);

        $client->request('GET', '/robots.txt');

        self::assertResponseIsSuccessful();
        $this->assertStringStartsWith('User-agent:*', $client->getResponse()->getContent(), 'robots.txt response should start with "User-agent:*"');
    }
}
