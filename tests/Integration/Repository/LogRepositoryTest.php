<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Integration\Repository;

use App\Entity\Log;
use App\Entity\SupportNumbersLandesliste;
use App\Repository\LogRepository;
use App\Tests\Builder\Entity\SupportNumbersBuilder;
use App\Tests\Builder\Entity\UserBuilder;
use App\Tests\Helper\RepositoryTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LogRepositoryTest extends KernelTestCase
{
    use RepositoryTestCase;
    private const string REPO_CLASS = LogRepository::class;
    private LogRepository $repo;

    public function testSave(): void
    {
        $this->assertEmpty($this->repo->findAll());

        $user = UserBuilder::build();

        $container = static::getContainer();
        $em = $container->get(EntityManagerInterface::class);
        $em->persist($user);
        $em->flush();
        $em->refresh($user);

        $supportNumbers = SupportNumbersBuilder::build(
            SupportNumbersLandesliste::TYPE,
            [
                'bundesland' => 'Berlin',
                'approved' => 1,
                'name' => 'Landesliste',
                'user' => $user,
            ]
        );
        $em->persist($supportNumbers);
        $em->flush();
        $em->refresh($supportNumbers);

        $log = new Log(
            $user,
            $supportNumbers,
            '10',
            'field1',
            '5'
        );
        $this->repo->save($log);

        $this->assertCount(1, $this->repo->findAll());
    }
}
