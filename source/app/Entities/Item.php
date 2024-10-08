<?php

namespace App\Entities;

readonly class Item
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
        public bool $isOldDesign,
        public bool $isDamaged,
    ) {
    }
}