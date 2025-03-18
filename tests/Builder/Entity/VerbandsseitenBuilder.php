<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Tests\Builder\Entity;

use App\Entity\Verbandsseiten;

class VerbandsseitenBuilder extends BaseBuilder
{
    /**
     * @param array{
     *     'name': string|null,
     *     'link': string|null,
     *     'linkName': string|null
     * } $args
     */
    public function build(array $args = []): Verbandsseiten
    {
        $verband = new Verbandsseiten();
        $verband->setName($args['name'] ?? self::faker()->name());
        $verband->setLink($args['link'] ?? self::faker()->url());
        $verband->setLinkName($args['linkName'] ?? \sprintf('%s Verband', $verband->getName()));

        return $verband;
    }
}
