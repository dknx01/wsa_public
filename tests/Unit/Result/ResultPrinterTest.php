<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Unit\Result;

use App\Configuration\Configuration;
use App\Result\ResultPrinter;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\NullAdapter;

class ResultPrinterTest extends TestCase
{
    private string $basePath;
    private string $configPath;
    private vfsStreamDirectory $root;

    protected function setUp(): void
    {
        $this->root = vfsStream::setup('exampleDir');
        $this->basePath = $this->root->url();
        $this->configPath = $this->root->url().'/wsa.config.php';
    }

    public function testGetResults(): void
    {
        $config = [
            'pageTitle' => 'Weltsozialamt',
            'impress' => 'Foo',
            'logoPath' => 'orga.svg',
            'pageLogoPath' => './logo.svg',
            'uuHelpWk' => 'wk.svg',
            'uuHelpLl' => 'll.svg',
            'resultFile' => 'result.json',
            'privacy' => 'Marty, I\'m sorry, but the only power source capable of generating 1.21 gigawatts of electricity is a bolt of lightning.',
            'resultAsStart' => null,
        ];
        file_put_contents($this->root->url().'/result.json', <<<JSON
{
  "BundeslandA": {
    "LL": "ZUGELASSEN (2596 UUs)",
    "WKs": [
          {
            "name": "Prignitz – Ostprignitz-Ruppin – Havelland I (WK 56)",
            "uus": 0,
            "zugelassen": 0
          }
      ]
  }
}
JSON
        );

        file_put_contents($this->configPath, '<?php return '.var_export($config, true).';');
        $configuration = new Configuration(
            new NullAdapter(),
            $this->configPath,
            $this->basePath,
            'test'
        );
        $printer = new ResultPrinter(new NullAdapter(), $configuration);
        foreach ($printer->getResults() as $result) {
            $this->assertEquals('BundeslandA', $result['state']);
        }
    }

    public function testGetResultsWithInvalidJson(): void
    {
        $config = [
            'pageTitle' => 'Weltsozialamt',
            'impress' => 'Foo',
            'logoPath' => 'orga.svg',
            'pageLogoPath' => './logo.svg',
            'uuHelpWk' => 'wk.svg',
            'uuHelpLl' => 'll.svg',
            'resultFile' => 'result.json',
            'privacy' => 'Marty, I\'m sorry, but the only power source capable of generating 1.21 gigawatts of electricity is a bolt of lightning.',
            'resultAsStart' => null,
        ];
        file_put_contents($this->root->url().'/result.json', <<<JSON
{
  "Hessen": {
    "LL": "Noch nicht geschafft"
  }
}
JSON
        );

        file_put_contents($this->configPath, '<?php return '.var_export($config, true).';');
        $configuration = new Configuration(
            new NullAdapter(),
            $this->configPath,
            $this->basePath,
            'test'
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/^Cannot\ parse\ result\ file.*/');
        $printer = new ResultPrinter(new NullAdapter(), $configuration);
        $f = $printer->getResults();
        foreach ($f as $result) {
            // do nothing, just for Generator call
        }
    }
}
