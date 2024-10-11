<?php

namespace App\Entities;

readonly class PriceListItemSetEntity
{
    public function __construct(
        public string $originalValue,
        public string $provider,
        public string $brand,
        public string $line,
    ) {
    }
}