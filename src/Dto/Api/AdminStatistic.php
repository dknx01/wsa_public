<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Dto\Api;

use Symfony\Component\Validator\Constraints as Assert;

class AdminStatistic
{
    public function __construct(
        #[Assert\NotBlank(message: 'State cannot be blank.')]
        public readonly string $state,
        public readonly string $area,
        #[Assert\NotBlank(message: 'UU cannot be blank.')]
        public string $uus,
        #[Assert\NotBlank(message: 'UU Unapproved cannot be blank.')]
        public string $uusUnapproved,
        #[Assert\NotBlank(message: 'Token cannot be blank.')]
        public readonly string $csrftoken,
        #[Assert\NotBlank(message: 'Type cannot be blank.')]
        public readonly string $type,
    ) {
    }
}
