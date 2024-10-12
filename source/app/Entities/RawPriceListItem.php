<?php

namespace App\Entities;

readonly class RawPriceListItem
{
    public function __construct(
        public string $article,
        public string $title,
        public float $price,
    ) {
    }
}