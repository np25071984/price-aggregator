<?php

namespace App\Entities;

readonly class PriceListItemEntity
{
    public function __construct(
        public string $originalValue,
        public string $provider,
        public string $brand,
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
    }
}