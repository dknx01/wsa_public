<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Dto;

enum WahlkreisType: string
{
    case BTW = 'Bundestagswahl';
    case LTW = 'Landtagswahl';
    case KW = 'Kommunalwahl';
    case KW_KOMMUNE = 'Kommunalwahl - Kommune';
    case KW_KREIS = 'Kommunalwahl - Kreis';
    case KW_REG_BEZIRK = 'Kommunalwahl - Regierungsbezirk';
    case KW_VERBAND = 'Kommunalwahl - Verband';
}
