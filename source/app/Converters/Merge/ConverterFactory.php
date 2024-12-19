<?php

namespace App\Converters\Merge;

use App\Converters\Merge\FestivalRubConverter;
use App\Converters\Merge\KurzinaRubConverter;
use App\Exceptions\UnknownFileException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Validators\FestivalRubValidator;
use App\Validators\KurzinaRubValidator;

class ConverterFactory
{
    public function determineConverter(Spreadsheet $spreadsheet): KurzinaRubConverter|FestivalRubConverter
    {
        return match (true) {
            (new KurzinaRubValidator)($spreadsheet) => new KurzinaRubConverter,
            (new FestivalRubValidator)($spreadsheet) => new FestivalRubConverter,
            default => throw new UnknownFileException("Couldn't determine merge converter"),
        };
    }
}