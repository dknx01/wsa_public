<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Application\Controller;

use App\Configuration\Configuration;
use App\Entity\DocumentDirektkandidat;
use App\Tests\Application\ApplicationTestCase;
use App\Tests\Builder\Entity\DocumentBuilder;
use App\Tests\Builder\Entity\SupportNumbersBuilder;
use App\Tests\Builder\Entity\VerbandsseitenBuilder;
use App\Tracking\MatomoConfig;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use function Symfony\Component\String\u;

class DefaultControllerTest extends ApplicationTestCase
{
    public function testFaq(): void
    {
        $this->startApplication();
        $this->client->request('GET', $this->getRouter()->generate(name: 'faq', referenceType: UrlGeneratorInterface::RELATIVE_PATH));

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h2', 'Häufige Fragen');
    }

    public function testDataPrivacy(): void
    {
        $this->startApplication();
        $crawler = $this->client->request('GET', $this->getRouter()->generate(name: 'dataPrivacy', referenceType: UrlGeneratorInterface::RELATIVE_PATH));

        $config = self::getContainer()->get(Configuration::class);
        $trackingConfig = self::getContainer()->get(MatomoConfig::class);

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('.card-header', 'Datenschutz');
        $privacyText = $trackingConfig->enabled
            ? $config->getPrivacy()
            : 'Es wird kein Besuch dieser Seite erfasst (getracked). Im eingeloggten Zustand ist eine Session-Id notwendig, um den eingeloggten Benutzer zu identifizieren. Es erfolgt keine weitere Verarbeitung der Benutzung der Seite.';
        $this->assertEquals(
            $privacyText,
            u($crawler->filterXPath('//*[@id="wsa wsa-board"]/main/main/div/div[2]/div/div[2]')->html())->trim()
        );
    }

    public function testStatisticsAll(): void
    {
        $this->startApplication();

        $supportNumber = SupportNumbersBuilder::build();
        $this->saveEntities([$supportNumber->getCreatedBy(), $supportNumber]);

        $this->client->request('GET', $this->getRouter()->generate('statistic_all_widget', [], UrlGeneratorInterface::RELATIVE_PATH));

        $this->assertStringContainsString($supportNumber->getBundesland(), $this->client->getResponse()->getContent());
    }

    public function testUuListe(): void
    {
        $this->startApplication();

        $landesliste = DocumentBuilder::build(args: ['state' => 'Foo']);
        $direktKandidat = DocumentBuilder::build(DocumentDirektkandidat::TYPE, args: ['state' => $landesliste->getState()]);
        $wahlkreis = $direktKandidat->getWahlKreis();
        $wahlkreis->setState($landesliste->getState());

        $this->saveEntities([
            (new VerbandsseitenBuilder())->build(['name' => $landesliste->getState()]),
            $direktKandidat->getWahlkreis(),
            $landesliste,
            $direktKandidat,
        ]);

        $this->client->request('GET', $this->getRouter()->generate('uu-liste', [], UrlGeneratorInterface::RELATIVE_PATH));

        $this->assertStringContainsString('Foo Verband', $this->client->getResponse()->getContent());
        $this->assertStringContainsString('Link zur Verbandsseite', $this->client->getResponse()->getContent());
        $this->assertStringContainsString('Landesliste', $this->client->getResponse()->getContent());
        $this->assertStringContainsString($wahlkreis->getName(), $this->client->getResponse()->getContent());
    }

    public function testImpressum(): void
    {
        $this->startApplication();
        $crawler = $this->client->request('GET', $this->getRouter()->generate(name: 'impressum', referenceType: UrlGeneratorInterface::RELATIVE_PATH));

        $config = self::getContainer()->get(Configuration::class);

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('.card-header', 'Impressum');
        $this->assertEquals(
            $config->getImpress(),
            u($crawler->filterXPath('//*[@id="wsa wsa-board"]/main/main/div/div[2]/div/div[2]')->html())->trim()
        );
    }
}
