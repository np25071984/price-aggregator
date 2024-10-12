<?php

namespace App\Converters;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

interface ConverterInterface
{
    /**
     * @return RawPriceListItem[]
     */
    public function convert(Spreadsheet $spreadsheet): array;
}