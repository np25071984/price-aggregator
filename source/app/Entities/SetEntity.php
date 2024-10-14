<?php

namespace App\Entities;

use App\Enums\PriceListProviderEnum;

readonly class SetEntity extends AbstractProductEntity
{
    public function __construct(
        string $article,
        string $originalTitle,
        float $price,
        PriceListProviderEnum $provider,
        public string $brand,
        public string $line,
    ) {
        parent::__construct(
            $article,
            $originalTitle,
            $price,
            $provider,
        );
    }
}