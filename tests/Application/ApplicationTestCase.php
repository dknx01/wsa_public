<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Application;

use App\Tests\Helper\DatabaseInitTrait;
use App\Tests\Helper\FakerTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\RouterInterface;

abstract class ApplicationTestCase extends WebTestCase
{
    use DatabaseInitTrait;
    use FakerTrait;

    protected KernelBrowser $client;

    protected function startApplication(): void
    {
        $this->client = WebTestCase::createClient(['environment' => 'test']);
        $this->initDatabase(KernelTestCase::$kernel);
    }

    protected function getRouter(): RouterInterface
    {
        /** @var RouterInterface $router */
        $router = KernelTestCase::$kernel->getContainer()->get('router');

        return $router;
    }

    /**
     * @param object[] $entities
     */
    protected function saveEntities(array $entities): void
    {
        $em = self::getContainer()->get(EntityManagerInterface::class);
        foreach ($entities as $entity) {
            $em->persist($entity);
        }
        $em->flush();
    }
}
