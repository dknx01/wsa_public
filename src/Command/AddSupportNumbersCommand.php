<?php

declare(strict_types=1);

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Command;

use App\Entity\SupportNumbersDirektkandidat;
use App\Entity\SupportNumbersLandesliste;
use App\Entity\User;
use App\Repository\SupportNumbersRepository;
use App\Repository\UserRepository;
use App\Repository\WahlkreisRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'wsa:add_support_numbers')]
class AddSupportNumbersCommand extends Command
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly SupportNumbersRepository $supportNumbersRepository,
        private readonly WahlkreisRepository $wahlkreisRepository,
        ?string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $question = $this->getHelper('question');
        /** @phpstan-ignore-next-line  */
        $userEmail = $question->ask(
            $input,
            $output,
            new Question('Please enter your email address: ')
        );
        /** @var ?User $user */
        $user = $this->userRepository->findOneBy(['email' => $userEmail]);
        if (null === $user) {
            $io->error('Email address not found.');

            return Command::FAILURE;
        }
        /** @phpstan-ignore-next-line  */
        $type = $question->ask($input, $output, new ChoiceQuestion('Type:', [
            SupportNumbersDirektkandidat::TYPE,
            SupportNumbersLandesliste::TYPE,
        ]));

        $supportNumbers = match ($type) {
            SupportNumbersDirektkandidat::TYPE => new SupportNumbersDirektkandidat(),
            SupportNumbersLandesliste::TYPE => new SupportNumbersLandesliste(),
            default => null,
        };
        if (!$supportNumbers) {
            $io->error('Support numbers not found.');

            return Command::FAILURE;
        }
        $supportNumbers->setCreatedBy($user);

        /** @phpstan-ignore-next-line  */
        $name = $question->ask($input, $output, new Question('Name: '));
        $supportNumbers->setName($name);
        /** @phpstan-ignore-next-line  */
        $approved = $question->ask($input, $output, new Question('Approved: '));
        $supportNumbers->setApproved((int) $approved);
        /** @phpstan-ignore-next-line  */
        $unapproved = $question->ask($input, $output, new Question('Unapproved: '));
        $supportNumbers->setUnapproved((int) $unapproved);

        /** @phpstan-ignore-next-line  */
        $wahlkreis = $question->ask($input, $output, new Question('Wahlkreis: '));
        if ('' !== $wahlkreis) {
            $wahlkreis = $this->wahlkreisRepository->findOneBy(['name' => $wahlkreis]);
        } else {
            $wahlkreis = null;
        }
        $supportNumbers->setWahlkreis($wahlkreis);

        $this->supportNumbersRepository->save($supportNumbers);

        return Command::SUCCESS;
    }
}
