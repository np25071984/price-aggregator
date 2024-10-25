<?php

namespace App\Entities;

readonly class RawPriceListItem
{
    public function __construct(
        public string $article,
        public string $originalTitle,
        public string $normalizedTitle,
        public float $price,
    ) {
    }
}