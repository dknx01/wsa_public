<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\User;

enum Roles: string
{
    case ROLE_USER = 'User';
    case ROLE_ADMIN = 'Admin';
    case ROLE_SUPER_ADMIN = 'Super-Admin';
}
