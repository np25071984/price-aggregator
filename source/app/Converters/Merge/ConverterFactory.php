<?php

namespace App\Converters\Merge;

use App\Converters\Merge\FestivalRusConverter;
use App\Converters\Merge\KurzinaRusConverter;
use App\Exceptions\UnknownFileException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Validators\FestivalRusValidator;
use App\Validators\KurzinaRusValidator;

class ConverterFactory
{
    public function determineConverter(Spreadsheet $spreadsheet): KurzinaRusConverter|FestivalRusConverter
    {
        return match (true) {
            (new KurzinaRusValidator)($spreadsheet) => new KurzinaRusConverter,
            (new FestivalRusValidator)($spreadsheet) => new FestivalRusConverter,
            default => throw new UnknownFileException("Couldn't determine merge converter"),
        };
    }
}