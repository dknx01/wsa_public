<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Statistic;

use Ramsey\Collection\AbstractCollection;

/**
 * @extends AbstractCollection<OverviewItem>
 */
class StatisticOverviewCollection extends AbstractCollection
{
    public function getType(): string
    {
        return OverviewItem::class;
    }
}
