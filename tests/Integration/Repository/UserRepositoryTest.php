<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Integration\Repository;

use App\Entity\SupportNumbersLandesliste;
use App\Entity\User;
use App\Repository\SupportNumbersRepository;
use App\Repository\UserRepository;
use App\Tests\Helper\RepositoryTestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SupportNumbersRepositoryTest extends KernelTestCase
{
    use RepositoryTestCase;

    private const string REPO_CLASS = SupportNumbersRepository::class;
    private SupportNumbersRepository $repo;

    public function testSave(): void
    {
        $this->assertEmpty($this->repo->findAll());

        $user = new User();
        $user->setEmail('test@test.com')
            ->setPassword('test');
        static::getContainer()->get(UserRepository::class)->save($user);

        $numbers = new SupportNumbersLandesliste();
        $numbers->setUpdatedAt(new \DateTimeImmutable())
            ->setCreatedBy($user)
            ->setName('Name A')
            ->setApproved(10);
        $this->repo->save($numbers);
        $this->assertCount(1, $this->repo->findAll());
    }
}
