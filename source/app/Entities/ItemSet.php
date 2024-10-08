<?php

namespace App\Entities;

readonly class ItemSet
{
    public function __construct(
        public string $originalValue,
        public string $provider,
        public string $brand,
        public string $line,
    ) {
    }
}