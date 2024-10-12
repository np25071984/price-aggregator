<?php

namespace App\Entities;

use App\Enums\PriceListProviderEnum;

readonly class PriceListItemEntity extends AbstractPriceListItemEntity
{
    public function __construct(
        string $article,
        string $originalTitle,
        float $price,
        PriceListProviderEnum $provider,
        public ?string $brand,
        public ?string $line,
        public ?float $volume, // TODO: enum
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