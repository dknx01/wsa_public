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

#[AsCommand(name: 'wsa:user:list', description: 'List users')]
class UserListCommand extends Command
{
    public function __construct(
        private readonly UserRepository $userRepo,
        ?string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('List users');

        $headers = ['Email', 'active'];
        $users = [];
        foreach ($this->userRepo->findAll() as $user) {
            $users[] = [
                'email' => $user->getEmail(),
                'active' => $user->isActive(),
            ];
        }

        $io->table($headers, $users);

        return Command::SUCCESS;
    }
}
