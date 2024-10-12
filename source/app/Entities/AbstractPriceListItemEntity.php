<?php

namespace App\Entities;
use App\Enums\PriceListProviderEnum;

abstract readonly class AbstractPriceListItemEntity
{
    public function __construct(
        public string $article,
        public string $originalTitle,
        public float $price,
        public PriceListProviderEnum $provider,
    ) {
    }
}