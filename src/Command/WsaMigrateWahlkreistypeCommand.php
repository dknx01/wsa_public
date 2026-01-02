<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Command;

use App\Entity\Wahlkreis;
use App\Repository\WahlkreisRepository;
use App\Repository\WahlkreistypRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'wsa:migrate:wahlkreistype',
    description: 'move wahlkreis type to table',
)]
class WsaMigrateWahlkreistypeCommand extends Command
{
    public function __construct(
        private readonly WahlkreistypRepository $wahlkreistypRepository,
        private readonly WahlkreisRepository $wahlkreisRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Wahlkreistype migration to new structure');

        /** @var Wahlkreis $wahlkreis */
        foreach ($this->wahlkreisRepository->findAll() as $wahlkreis) {
            $wahlkreis->setWahlkreistyp($this->wahlkreistypRepository->findOneBy(['abbreviation' => $wahlkreis->getType()->name]));
            $wahlkreis->setType(null);
            $this->wahlkreisRepository->save($wahlkreis);
        }

        return self::SUCCESS;
    }
}
