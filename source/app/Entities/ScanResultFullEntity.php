<?php

namespace App\Entities;

use App\Enums\SubStringPositionEnum;

readonly class ScanResultFullEntity extends ScanResultEntity
{
    public function __construct(
        string $dictionaryValue,
        SubStringPositionEnum $positionInScannedString,
        public string|float $unifiedValue,
    ) {
        parent::__construct($dictionaryValue, $positionInScannedString);
    }
}