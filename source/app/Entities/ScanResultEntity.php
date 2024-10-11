<?php

namespace App\Entities;

use App\Enums\SubStringPositionEnum;

readonly class ScanResultEntity
{
    public function __construct(
        public string $dictionaryValue,
        public SubStringPositionEnum $positionInScannedString,
    ) {
    }
}