<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Statistic;

final readonly class StatisticDataItem
{
    public function __construct(
        public ?string $id,
        public int $approved,
        public int $unapproved,
        public string $name,
        public string $createdBy,
        public string $createdAt,
        public string $updatedAt,
        public string $type,
        public string $state,
        public string $comment,
    ) {
    }
}
