<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Unit\Twig\Extension;

use App\Configuration\Configuration;
use App\Twig\Extension\ConfigurationExtension;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\NullAdapter;

class ConfigurationExtensionTest extends TestCase
{
    public function testGetFunctions(): void
    {
        $root = vfsStream::setup('exampleDir');
        $basePath = $root->url();
        $configPath = $root->url().'/wsa.config.php';
        $config = [
            'pageTitle' => 'Weltsozialamt',
            'impress' => 'Foo',
            'logoPath' => 'orga.svg',
            'pageLogoPath' => './logo.svg',
            'uuHelpWk' => 'wk.svg',
            'uuHelpLl' => 'll.svg',
            'privacy' => 'Wow! Sporty go-cart, Leela! So hip and sexy. Not like you at all.',
            'resultAsStart' => null,
            'resultFile' => 'result.file',
        ];
        file_put_contents($root->url().'/logo.svg', 'x123y');
        file_put_contents($root->url().'/ll.svg', 'x123y');
        file_put_contents($root->url().'/wk.svg', 'x123y');
        file_put_contents($root->url().'/orga.svg', 'x123y');

        file_put_contents($configPath, '<?php return '.var_export($config, true).';');

        $configuration = new Configuration(
            new NullAdapter(),
            $configPath,
            $basePath,
            'test'
        );
        $extension = new ConfigurationExtension($configuration);

        $this->assertEquals('Foo', $extension->impress());
        $this->assertEquals('x123y', $extension->organisationLogo());
        $this->assertEquals('x123y', $extension->logo());
        $this->assertEquals('Weltsozialamt', $extension->title());

        $this->assertStringStartsWith('data:image/png;base64', $extension->uuHelpImage('wk'));
        $this->assertStringStartsWith('data:image/png;base64', $extension->uuHelpImage('ll'));
        $this->assertStringStartsWith('data:image/png;base64', $extension->uuHelpImage('foo'));
    }
}
