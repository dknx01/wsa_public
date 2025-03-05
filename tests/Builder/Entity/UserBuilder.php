<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Builder\Entity;

use App\Entity\User;

class UserBuilder extends BaseBuilder
{
    /**
     * @param array{
     *     email: string|null,
     *     password: string|null
     * } $args
     */
    public static function build(array $args = []): User
    {
        $user = new User();
        $user->setEmail($args['email'] ?? self::faker()->safeEmail());
        $user->setPassword($args['password'] ?? self::faker()->password());

        return $user;
    }

    /**
     * @param array{
     *     email: string|null,
     *     password: string|null,
     *     id: int
     * } $args
     */
    public static function buildWithId(array $args = []): User
    {
        $user = new User();
        $user->setEmail($args['email'] ?? self::faker()->safeEmail());
        $user->setPassword($args['password'] ?? self::faker()->password());
        $reflection = new \ReflectionClass($user);
        $reflectionProperty = $reflection->getProperty('id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($user, $args['id'] ?? 1);

        return $user;
    }
}
