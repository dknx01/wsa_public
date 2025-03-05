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
use Twig\TwigFunction;

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
            'resultFile' => 'result.json',
        ];
        file_put_contents($root->url().'/logo.svg', 'x123y');
        file_put_contents($root->url().'/ll.svg', 'x123y');
        file_put_contents($root->url().'/wk.svg', 'x123y');
        file_put_contents($root->url().'/orga.svg', 'x123y');

        file_put_contents($configPath, '<?php return '.var_export($config, true).';');

        $configuration = new Configuration(new NullAdapter(), $configPath, $basePath);
        $extension = new ConfigurationExtension($configuration);
        /** @var TwigFunction[] $functions */
        $functions = $extension->getFunctions();
        $this->assertEquals('Foo', $functions[0]->getCallable()());
        $this->assertEquals('x123y', $functions[1]->getCallable()());
        $this->assertEquals('x123y', $functions[2]->getCallable()());
        $this->assertEquals('Weltsozialamt', $functions[3]->getCallable()());

        $this->assertStringStartsWith('data:image/png;base64', $functions[4]->getCallable()('wk'));
        $this->assertStringStartsWith('data:image/png;base64', $functions[4]->getCallable()('ll'));
        $this->assertStringStartsWith('data:image/png;base64', $functions[4]->getCallable()('foo'));
    }
}
