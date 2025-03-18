<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Application\Controller;

use App\Configuration\ConfigurationItem;
use App\Entity\DocumentDirektkandidat;
use App\Tests\Application\ApplicationTestCase;
use App\Tests\Builder\Entity\DocumentBuilder;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class WsaControllerTest extends ApplicationTestCase
{
    public function testSearch(): void
    {
        $this->startApplication();
        $this->saveEntities([DocumentBuilder::build()]);

        $this->client->request(
            'GET',
            $this->getRouter()->generate('documentsSearch', ['search' => '-'], UrlGeneratorInterface::NETWORK_PATH)
        );

        self::assertResponseIsSuccessful();
    }

    public function testResults(): void
    {
        $this->startApplication();

        $this->client->request('GET', $this->getRouter()->generate(name: 'results', referenceType: UrlGeneratorInterface::NETWORK_PATH));

        self::assertResponseIsSuccessful();
    }

    public function testHelp(): void
    {
        $this->startApplication();

        $this->client->request('GET', $this->getRouter()->generate(name: 'help', referenceType: UrlGeneratorInterface::NETWORK_PATH));

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains(
            '.header-item-title > span',
            'Hier findest du Anleitungen zur Unterstützung beim Ausfüllen der Unterstützungsunterschriften'
        );
    }

    public function testDownloadFailed(): void
    {
        $this->startApplication();

        $this->client->request(
            'GET',
            $this->getRouter()->generate('documentDownload', ['id' => Uuid::uuid4()->toString()], UrlGeneratorInterface::NETWORK_PATH)
        );
        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testDownloadSuccessful(): void
    {
        $this->startApplication();

        $document = DocumentBuilder::build();
        $this->saveEntities([$document]);

        $fs = new Filesystem();
        $folder = \sprintf(
            '%s/%s',
            __DIR__.'/../../../var',
            'docs',
        );
        $fs->mkdir(\dirname($folder));
        $file = \sprintf(
            '%s/%s.pdf',
            $folder,
            $document->getId()->toString()
        );
        $fs->touch($file);
        $fs->appendToFile($file, 'test');

        $this->client->request(
            'GET',
            $this->getRouter()->generate('documentDownload', ['id' => $document->getId()->toString()], UrlGeneratorInterface::NETWORK_PATH)
        );
        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $fs->remove($file);
    }

    public function testHomeWithRedirect(): void
    {
        $this->startApplication();

        /** @var CacheInterface $cache */
        $cache = self::getContainer()->get(CacheInterface::class);
        $cache->delete('wsa_configuration');
        $configData = [
            'pageTitle' => 'Weltsozialamt',
            'impress' => 'Das Impressum',
            'logoPath' => './assets/images/Live.svg',
            'pageLogoPath' => './assets/images/Logo.svg',
            'uuHelpWk' => './assets/images/Ausfuellhilfe-UU-Wahlkreise.png',
            'uuHelpLl' => './assets/images/Ausfuellhilfe-UU-Landesliste.png',
            'resultFile' => './results.json',
            'privacy' => 'Datenschutz und Tracking Hinweis',
            'resultAsStart' => ['test'],
        ];
        $configurationItem = new ConfigurationItem(...$configData);
        $cache->get('wsa_configuration', function (ItemInterface $item, bool $save) use ($configurationItem) {
            $item->expiresAt((new \DateTimeImmutable())->modify('+1 seconds'));

            return $configurationItem;
        });

        $this->client->request(
            'GET',
            $this->getRouter()->generate(name: 'home', referenceType: UrlGeneratorInterface::NETWORK_PATH)
        );
        $this->client->followRedirect();

        self::assertResponseIsSuccessful();
    }

    public function testHomeWithDocuments(): void
    {
        $this->startApplication();
        /** @var CacheInterface $cache */
        $cache = self::getContainer()->get(CacheInterface::class);
        $cache->delete('wsa_configuration');

        $document1 = DocumentBuilder::build(args: ['state' => 'Nordrhein-Westfalen']);
        $document2 = DocumentBuilder::build(args: ['state' => 'Rheinland-Pfalz']);
        $document3 = DocumentBuilder::build(args: ['state' => 'Berlin']);
        $this->saveEntities([$document1, $document2, $document3]);

        $this->client->request(
            'GET',
            $this->getRouter()->generate(name: 'home', referenceType: UrlGeneratorInterface::NETWORK_PATH)
        );

        self::assertResponseIsSuccessful();
    }

    public function testGetByState(): void
    {
        $this->startApplication();

        $document1 = DocumentBuilder::build();
        $document2 = DocumentBuilder::build(DocumentDirektkandidat::TYPE, ['state' => $document1->getState()]);
        $this->saveEntities([$document1, $document2->getWahlkreis(), $document2]);

        $this->client->request(
            'GET',
            $this->getRouter()->generate('documentStateList', ['state' => $document1->getState()], UrlGeneratorInterface::NETWORK_PATH)
        );

        self::assertResponseIsSuccessful();
    }
}
