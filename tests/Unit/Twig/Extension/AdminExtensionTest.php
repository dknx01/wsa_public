<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Unit\Twig\Extension;

use App\Entity\User;
use App\Tests\Builder\Entity\UserBuilder;
use App\Twig\Extension\AdminExtension;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class AdminExtensionTest extends TestCase
{
    public static function provideUser(): \Generator
    {
        yield [
            '',
            null,
        ];
        $user = UserBuilder::build();
        yield [
            $user->getEmail(),
            $user,
        ];
    }

    #[TestWith(['', null])]
    #[TestWith(['Foo', 'Foo'])]
    public function testDisplayString(string $expected, ?string $value): void
    {
        $extension = new AdminExtension();
        $this->assertEquals($expected, $extension->displayString($value));
    }

    #[DataProvider('provideUser')]
    public function testDisplayUserEmail(string $expected, ?User $value): void
    {
        $extension = new AdminExtension();
        $this->assertEquals($expected, $extension->displayUserEmail($value));
    }
}
