<?php

declare(strict_types=1);

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Command;

use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'wsa:user:password', description: 'change a user password')]
class UserChangePasswordCommand extends Command
{
    public function __construct(
        private readonly UserRepository $userRepo,
        private readonly UserPasswordHasherInterface $hasher,
        ?string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Change a users password');
        $email = $io->ask('Enter the email address');
        $passwordOld = $io->askHidden('Enter the old password');
        $password = $io->askHidden('Enter the new password');
        $user = $this->userRepo->findOneBy(['email' => $email]);
        if (!$user) {
            $io->error('User not found');

            return Command::FAILURE;
        }
        if ($user->getPassword() !== $this->hasher->hashPassword($user, $passwordOld)) {
            $io->error('Old password is incorrect');

            return Command::FAILURE;
        }
        $user->setPassword($this->hasher->hashPassword($user, $password));

        $this->userRepo->save($user);
        $io->success('User password changed');

        return Command::SUCCESS;
    }
}
