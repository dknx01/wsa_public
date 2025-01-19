<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Integration\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\Helper\RepositoryTestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class UserRepositoryTest extends KernelTestCase
{
    use RepositoryTestCase;

    private const string REPO_CLASS = UserRepository::class;
    private UserRepository $repo;

    public function testSave(): void
    {
        $this->assertEmpty($this->repo->findAll());

        $user = new User();
        $user->setEmail('test@test.com')
            ->setPassword('test');
       $this->repo->save($user);

        $this->assertCount(1, $this->repo->findAll());
    }
    public function testEmailExists(): void
    {
        $this->assertEmpty($this->repo->findAll());

        $user = new User();
        $user->setEmail('test@test.com')
            ->setPassword('test');
       $this->repo->save($user);

        $this->assertTrue($this->repo->emailExists('test@test.com'));
        $this->assertFalse($this->repo->emailExists(null));
        $this->assertFalse($this->repo->emailExists('foo@bar.text'));
    }
    public function testUpgradePassword(): void
    {
        $this->assertEmpty($this->repo->findAll());

        $user = new User();
        $user->setEmail('test@test.com')
            ->setPassword('test');
       $this->repo->save($user);
       $this->repo->upgradePassword($user, 'nopassword123');
       $this->assertSame('nopassword123', $this->repo->findAll()[0]->getPassword());
    }
    public function testUpgradePasswordInvalidUser(): void
    {
        $this->expectException(UnsupportedUserException::class);
        $user = new class implements PasswordAuthenticatedUserInterface {
            public function getPassword(): string
            {
                return '';
            }

        };
        $this->repo->upgradePassword($user, 'nopassword123');
    }
}
