<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Unit\Security;

use App\Entity\User;
use App\Security\ActiveUserVoter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\TestBrowserToken;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ActiveUserVoterTest extends TestCase
{
    public static function provideInvalid(): \Generator
    {
        yield [new TestBrowserToken(user: new User())];
        yield [new TestBrowserToken()];
    }

    #[DataProvider('provideInvalid')]
    public function testVoteFailed(mixed $token): void
    {
        $voter = new ActiveUserVoter();
        $this->assertSame(Voter::ACCESS_DENIED, $voter->vote($token, new \stdClass(), [ActiveUserVoter::ACTIVE_USER]));
    }

    public function testVoteSuccess(): void
    {
        $voter = new ActiveUserVoter();
        $user = new User();
        $user->setActive(true);
        $token = new TestBrowserToken(user: $user);
        $this->assertSame(Voter::ACCESS_GRANTED, $voter->vote($token, new \stdClass(), [ActiveUserVoter::ACTIVE_USER]));
    }
}
