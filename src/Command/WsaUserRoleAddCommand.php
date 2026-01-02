<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Command;

use App\Repository\UserRepository;
use App\User\Roles;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'wsa:user:role:add',
    description: 'Add a role to a user',
)]
class WsaUserRoleAddCommand extends Command
{
    public function __construct(private readonly UserRepository $userRepository)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Add a role to a user');

        $email = $io->ask('Enter the email address');
        $user = $this->userRepository->findOneBy(['email' => $email]);
        if (!$user) {
            $io->error('User not found');

            return Command::FAILURE;
        }
        $io->info('Users current role');
        $io->listing($user->getRoles());
        $question = new ChoiceQuestion('Role', [Roles::ROLE_ADMIN->name, Roles::ROLE_USER->name, Roles::ROLE_SUPER_ADMIN->name]);
        $role = $io->askQuestion($question);
        $user->setRoles([$role]);
        $this->userRepository->save($user);

        return Command::SUCCESS;
    }
}
