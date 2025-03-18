<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Application\Controller;

use App\Tests\Application\ApplicationTestCase;
use App\Tests\Builder\Entity\SupportNumbersBuilder;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StatisticControllerTest extends ApplicationTestCase
{
    public function testStateData(): void
    {
        $this->startApplication();

        $supportNumbers = SupportNumbersBuilder::build();
        $this->saveEntities([
            $supportNumbers->getCreatedBy(),
            $supportNumbers,
        ]);

        $this->client->request(
            'GET',
            $this->getRouter()->generate('app_new_statistic_statedata', ['state' => $supportNumbers->getBundesland()], UrlGeneratorInterface::NETWORK_PATH)
        );

        self::assertResponseIsSuccessful();

        $this->assertJson($this->client->getResponse()->getContent());
    }

    public function testOverview(): void
    {
        $this->startApplication();

        $supportNumbers = SupportNumbersBuilder::build(data: ['bundesland' => self::createFaker()->bundeslandExclude(['Nordrhein-Westfalen', 'Rheinland-Pfalz'])]);
        $supportNumbers1 = SupportNumbersBuilder::build(data: ['bundesland' => 'Nordrhein-Westfalen']);
        $supportNumbers2 = SupportNumbersBuilder::build(data: ['bundesland' => 'Rheinland-Pfalz']);
        $this->saveEntities([
            $supportNumbers->getCreatedBy(),
            $supportNumbers,
            $supportNumbers1->getCreatedBy(),
            $supportNumbers1,
            $supportNumbers2->getCreatedBy(),
            $supportNumbers2,
        ]);

        $this->client->request(
            'GET',
            $this->getRouter()->generate(name: 'new_statistic', referenceType: UrlGeneratorInterface::NETWORK_PATH)
        );

        self::assertResponseIsSuccessful();
    }
}
