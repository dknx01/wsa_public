<?php

declare(strict_types=1);

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'wsa:user:activate', description: '(De)Activate a user')]
class UserActivateCommand extends Command
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
        $io->title('(De)Activate a user');
        $email = $io->ask('Enter the email address');
        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            'Is the user active?',
            ['yes', 'no'],
            0
        );
        $question->setErrorMessage('Answer %s is invalid.');

        /** @phpstan-ignore-next-line */
        $active = $helper->ask($input, $output, $question);
        /** @var User|null $user */
        $user = $this->userRepo->findOneBy(['email' => $email]);
        if (!$user) {
            $io->error('User not found');

            return Command::FAILURE;
        }
        $user->setActive($active);

        $this->userRepo->save($user);
        $io->success('User status changed');

        return Command::SUCCESS;
    }
}
