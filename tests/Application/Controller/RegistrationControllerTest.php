<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Application\Controller;

use App\Repository\UserRepository;
use App\Tests\Application\ApplicationTestCase;
use App\Tests\Builder\Entity\UserBuilder;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RegistrationControllerTest extends ApplicationTestCase
{
    public function testRegister(): void
    {
        $this->startApplication();
        $this->initDatabase(self::$kernel);
        $this->client->request('GET', $this->getRouter()->generate(name: 'app_register', referenceType: UrlGeneratorInterface::NETWORK_PATH));

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Registrierung');

        $this->client->submitForm('Registrieren', [
            'registration_form[plainPassword]' => '123456',
            'registration_form[email]' => 'test@test.com',
        ]);
        $this->client->followRedirect();
        $this->assertNotNull(self::getContainer()->get(UserRepository::class)->findOneBy(['email' => 'test@test.com']));
        $this->assertStringContainsString(
            'Registrierung erfolgreich',
            $this->client->getResponse()->getContent()
        );
    }

    public function testRegisterForKnownUser(): void
    {
        $this->startApplication();
        $this->initDatabase(self::$kernel);

        /** @var UserPasswordHasherInterface $hasher */
        $hasher = self::getContainer()->get(UserPasswordHasherInterface::class);
        $user = UserBuilder::build(['email' => 'test@test.com']);
        $this->saveEntities([$user]);
        $user->setPassword($hasher->hashPassword($user, '123456'));
        $this->saveEntities([$user]);

        $this->client->request('GET', $this->getRouter()->generate(name: 'app_register', referenceType: UrlGeneratorInterface::NETWORK_PATH));
        $this->client->submitForm('Registrieren', [
            'registration_form[plainPassword]' => '123456',
            'registration_form[email]' => 'test@test.com',
        ]);

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Registrierung');

        $this->client->submitForm('Registrieren', [
            'registration_form[plainPassword]' => '123456',
            'registration_form[email]' => 'test@test.com',
        ]);
        $this->assertStringContainsString('Benutzer existiert bereits.', $this->client->getResponse()->getContent());
    }

    public function testReloadCaptcha(): void
    {
        $this->startApplication();
        $this->initDatabase(self::$kernel);

        $this->client->request('GET', $this->getRouter()->generate(name: 'registration_reloadcaptcha', referenceType: UrlGeneratorInterface::NETWORK_PATH));
        $this->assertJson($this->client->getResponse()->getContent());
        $this->assertStringContainsString('captcha', $this->client->getResponse()->getContent());
    }
}
