<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Unit\Configuration;

use App\Configuration\Configuration;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\NullAdapter;
use Symfony\Contracts\Cache\CacheInterface;

class ConfigurationTest extends TestCase
{
    private vfsStreamDirectory $root;
    private CacheInterface $cache;
    private string $configPath;
    private string $basePath;
    private array $config = [
        'pageTitle' => 'Weltsozialamt',
        'impress' => 'Foo',
        'logoPath' => '',
        'pageLogoPath' => '',
        'uuHelpWk' => '',
        'uuHelpLl' => '',
        'resultFile' => '',
        'privacy' => 'Marty, I\'m sorry, but the only power source capable of generating 1.21 gigawatts of electricity is a bolt of lightning.',
        'resultAsStart' => null,
    ];
    private string $env = 'test';

    protected function setUp(): void
    {
        $this->root = vfsStream::setup('exampleDir');
        $this->basePath = $this->root->url();
        $this->configPath = $this->root->url().'/wsa.config.php';
        $this->cache = new NullAdapter();
    }

    public function testGetLogo(): void
    {
        $this->config['pageLogoPath'] = './logo.svg';
        file_put_contents($this->root->url().'/logo.svg', 'x123y');
        $this->assertEquals('x123y', $this->getConfiguration()->getLogo());
    }

    public function testGetImpress(): void
    {
        $this->assertEquals('Foo', $this->getConfiguration()->getImpress());
    }

    public function testGetUuLLAsBase64(): void
    {
        $this->config['uuHelpLl'] = './ll.svg';
        file_put_contents($this->root->url().'/ll.svg', 'x123y');
        $this->assertStringStartsWith('data:image/png;base64', $this->getConfiguration()->getUuLLAsBase64());
    }

    public function testGetPageTitle(): void
    {
        $this->assertSame('Weltsozialamt', $this->getConfiguration()->getPageTitle());
    }

    public function testGetUuWkAsBase64(): void
    {
        $this->config['uuHelpWk'] = './wk.svg';
        file_put_contents($this->root->url().'/wk.svg', 'x123y');
        $this->assertStringStartsWith('data:image/png;base64', $this->getConfiguration()->getUuWkAsBase64());
    }

    public function testGetOrganisationLogo(): void
    {
        $this->config['logoPath'] = './orga.svg';
        file_put_contents($this->root->url().'/orga.svg', 'x123y');
        $this->assertEquals('x123y', $this->getConfiguration()->getOrganisationLogo());
    }

    public function testGetResultFile(): void
    {
        $this->config['resultFile'] = './result.json';
        $this->assertEquals('vfs://exampleDir/./result.json', $this->getConfiguration()->getResultFile());
    }

    #[TestWith(['ll'])]
    #[TestWith(['wk'])]
    #[TestWith(['foo'])]
    public function testGetUuHelp(string $type): void
    {
        $this->config['uuHelpWk'] = './wk.svg';
        file_put_contents($this->root->url().'/wk.svg', 'x123y');
        $this->config['uuHelpLl'] = './ll.svg';
        file_put_contents($this->root->url().'/ll.svg', 'x123y');
        $this->assertStringStartsWith('data:image/png;base64', $this->getConfiguration()->getUuHelp($type));
    }

    #[TestWith([false, 'test', null])]
    #[TestWith([false, 'test', ['dev']])]
    #[TestWith([true, 'test', ['dev', 'test']])]
    public function testResultAsStart(bool $expected, string $env, ?array $resultAsStart): void
    {
        $this->config['resultAsStart'] = $resultAsStart;
        $this->env = $env;
        $this->assertEquals($expected, $this->getConfiguration()->resultsAsStart());
    }

    public function testGetPrivacy(): void
    {
        $this->assertNotEmpty($this->getConfiguration()->getPrivacy());
    }

    private function getConfiguration(): Configuration
    {
        file_put_contents($this->configPath, '<?php return '.var_export($this->config, true).';');

        return new Configuration($this->cache, $this->configPath, $this->basePath, $this->env);
    }
}
