<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Integration\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\Helper\DatabaseInitTrait;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class UserListCommandTest extends KernelTestCase
{
    use DatabaseInitTrait;

    public function testExecute(): void
    {
        $kernel = self::bootKernel();
        $this->initDatabase($kernel);
        $user = new User();
        $user->setEmail('test@test.com')
            ->setPassword('test');
        static::getContainer()->get(UserRepository::class)->save($user);

        $application = new Application($kernel);

        $command = $application->find('wsa:user:list');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('test@test.com', $output);
    }
}
