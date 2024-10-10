<?php

namespace App\Converters;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

interface ConverterInterface
{
    public function convert(Spreadsheet $spreadsheet, string $firstColumnValue): array;
}