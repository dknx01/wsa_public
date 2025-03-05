<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\User;

use Symfony\Component\HttpFoundation\Request;

readonly class EditDvo
{
    /**
     * @param array{
     *     string?: 'active'
     * } $wahlkreis
     */
    public function __construct(
        public ?string $email = null,
        public bool $active = false,
        public array $wahlkreis = [],
        public ?string $role = null,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(...\array_key_exists('user', $request->request->all()) ? $request->request->all()['user'] : ['email' => null, 'role' => null]);
    }
}
