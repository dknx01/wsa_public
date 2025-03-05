<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Helper;

trait RepositoryTestCase
{
    use DatabaseInitTrait;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        if ('test' !== $kernel->getEnvironment()) {
            throw new \LogicException('Execution only in Test environment possible!');
        }

        $this->initDatabase($kernel);

        $container = static::getContainer();
        $this->repo = $container->get(self::REPO_CLASS);
    }
}
