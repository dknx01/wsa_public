<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Unit\User;

use App\User\EditDvo;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Uid\Ulid;

class EditDvoTest extends TestCase
{
    public static function provideRequest(): \Generator
    {
        yield [new Request(request: [
            'user' => [
                'role' => 'ROLE_A',
                'active' => true,
                'email' => 'test@test.com',
                'wahlkreis' => [new Ulid(), new Ulid()],
            ],
        ]
        )];
        yield [new Request(request: [])];
    }

    #[DataProvider('provideRequest')]
    public function testFromRequest(Request $request): void
    {
        $dvo = EditDvo::fromRequest($request);
        $userData = \array_key_exists('user', $request->request->all()) ? $request->request->all()['user'] : [];
        $this->assertSame($userData['role'] ?? null, $dvo->role);
        $this->assertSame($userData['active'] ?? false, $dvo->active);
        $this->assertSame($userData['email'] ?? null, $dvo->email);
        $this->assertSame($userData['wahlkreis'] ?? [], $dvo->wahlkreis);
    }
}
