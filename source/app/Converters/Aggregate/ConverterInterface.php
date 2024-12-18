<?php

namespace App\Converters\Aggregate;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

interface ConverterInterface
{
    /**
     * @return RawPriceListItem[]
     */
    public function convert(Spreadsheet $spreadsheet): array;
}