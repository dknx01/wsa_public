<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\SupportNumbers;

enum Type: string
{
    case LL_BTW = 'Landesliste (BTW)';
    case DK_BTW = 'Direktkandidat (BTW)';
    case LL_KW = 'Liste (Kommunalwahl)';
    case DK_KW = 'Direktkandidat (Kommunalwahl)';
    case LL_LTW = 'Landesliste (Landeswahl)';
    case DK_LTW = 'Direktkandidat (Landeswahl)';
}
