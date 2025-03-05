<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Unit\Command;

use App\Command\UserAgentListCommand;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\NullOutput;

class UserAgentListCommandTest extends TestCase
{
    /**
     * @throws ExceptionInterface
     * @throws \JsonException
     */
    public function testExecute(): void
    {
        $inputPath = \dirname(__DIR__, 3).'/data/useragents.conf.json';
        $root = vfsStream::setup('exampleDir');

        $outputPath = $root->url().'/output.txt';
        $command = new UserAgentListCommand($inputPath, $outputPath);

        $command->run(new ArrayInput([]), new NullOutput());

        $this->assertNotEmpty(file_get_contents($outputPath));
    }

    /**
     * @throws ExceptionInterface
     * @throws \JsonException
     */
    public function testExecuteWithError(): void
    {
        $root = vfsStream::setup('exampleDir');
        $inputPath = $root->url().'/data/useragents.conf.json';
        $outputPath = $root->url().'/output.txt';
        $command = new UserAgentListCommand($inputPath, $outputPath);
        $output = new BufferedOutput();
        $command->run(new ArrayInput([]), $output);
        $this->assertStringContainsString('Failed to read file', $output->fetch());
    }
}
