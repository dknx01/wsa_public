<?php

declare(strict_types=1);

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Command;

use App\Entity\Statistic;
use App\Entity\SupportNumbersDirektkandidat;
use App\Entity\SupportNumbersLandesliste;
use App\Repository\StatisticRepository;
use App\Repository\UserRepository;
use App\Repository\WahlkreisRepository;
use App\SupportNumbers\SupportNumbersService;
use Doctrine\ORM\Exception\ORMException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressIndicator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function Symfony\Component\String\u;

#[AsCommand(name: 'wsa:statistic:convert', description: 'Hello PhpStorm')]
class StatisticConvertCommand extends Command
{
    public function __construct(
        private readonly StatisticRepository $statisticRepo,
        private readonly SupportNumbersService $supportNumbersService,
        private readonly UserRepository $userRepo,
        private readonly WahlkreisRepository $wahlkreisRepo,
        ?string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Convert statistic data into support numbers');
        $user = $this->userRepo->findAll()[0];
        $progressIndicator = new ProgressIndicator($output, finishedIndicatorValue: '✅');
        $progressIndicator->start('Converting...');
        /** @var Statistic $statistic */
        foreach ($this->statisticRepo->findAll() as $statistic) {
            $supportNumber = match ($statistic->getType()) {
                'Direktkandidaten' => new SupportNumbersDirektkandidat(),
                default => new SupportNumbersLandesliste(),
            };
            $supportNumber->setName($statistic->getName())
                ->setUnapproved($statistic->getUnapproved())
                ->setApproved($statistic->getApproved())
                ->setCreatedBy($user)
                ->setBundesland($statistic->getBundesland())
                ->setUpdatedAt($statistic->getUpdatedAt())
                ->setUpdatedBy($user);
            if ($supportNumber instanceof SupportNumbersDirektkandidat) {
                $wkName = u($statistic->getName())->before('(Nr.')->trim();
                $wahlkreis = $this->wahlkreisRepo->findOneBy(['name' => $wkName->toString(), 'state' => $statistic->getBundesland()]);
                $supportNumber->setWahlkreis($wahlkreis);
            }
            try {
                $this->supportNumbersService->save($supportNumber, $user);
            } catch (ORMException|InvalidArgumentException $e) {
                $io->error($e->getMessage());
                $progressIndicator->finish('Failed', '🚨');

                return Command::FAILURE;
            }
            $progressIndicator->advance();
        }
        $progressIndicator->finish('Finished');
        $io->success('Statistic converted successfully');

        return Command::SUCCESS;
    }
}
