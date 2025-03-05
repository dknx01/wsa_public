<?php

declare(strict_types=1);

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

use function Symfony\Component\String\u;

#[AsCommand(name: 'wsa:user_agent:generate', description: 'generating the user agent list from the json config file')]
class UserAgentListCommand extends Command
{
    public function __construct(
        #[Autowire('%kernel.project_dir%/data/useragents.conf.json')] private string $configPath,
        #[Autowire('%kernel.project_dir%/data/useragents.txt')] private string $outpath,
    ) {
        parent::__construct(null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Generating user agent list from json config file');

        $fs = new Filesystem();
        try {
            $json = json_decode($fs->readFile($this->configPath), true, 512, \JSON_THROW_ON_ERROR);
            $fs->remove($this->outpath);
            $fs->touch($this->outpath);
            foreach ($json as $userAgent => $data) {
                $fs->appendToFile($this->outpath, u($userAgent)->replace(' ', '\ ')->toString().\PHP_EOL);
            }
            $io->success('Generated user agent list successfully');
        } catch (\JsonException|IOException $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
