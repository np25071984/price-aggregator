<?php

namespace App\Entities\Products;

use App\Enums\PriceListProviderEnum;

readonly class PerfumeEntity extends AbstractProductEntity
{
    public function __construct(
        string $article,
        string $originalTitle,
        float $price,
        PriceListProviderEnum $provider,
        public ?string $brand,
        public ?string $line,
        public string $volume, // public ?float $volume, // TODO: enum
        public ?string $type, // TODO: enum
        public ?string $sex, // TODO: enum
        public bool $isArtisanalBottling,
        public bool $hasMarking,
        public bool $isTester,
        public bool $isSample,
        public bool $isOldDesign,
        public bool $isRefill,
        public bool $isDamaged,
    ) {
        parent::__construct(
            $article,
            $originalTitle,
            $price,
            $provider,
        );
    }
}