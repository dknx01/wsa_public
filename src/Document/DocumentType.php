<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Document;

enum DocumentType: string
{
    case LANDESLISTE = 'Landesliste';
    case DIREKTKANDIDAT_BTW = 'DirektKandidat Btw';
}
