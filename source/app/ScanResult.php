<?php

namespace App;

use App\Enums\SubStringPositionEnum;

readonly class ScanResult
{
    public function __construct(
        public string $dictionaryValue,
        public SubStringPositionEnum $positionInScannedString,
    ) {
    }
}