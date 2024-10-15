<?php

namespace App\Entities\Products;

use App\Enums\PriceListProviderEnum;

readonly class UnknownProductEntity extends AbstractProductEntity
{
    public function __construct(
        string $article,
        string $originalTitle,
        float $price,
        PriceListProviderEnum $provider,
    ) {
        parent::__construct(
            $article,
            $originalTitle,
            $price,
            $provider,
        );
    }
}