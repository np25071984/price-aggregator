<?php

namespace App\Converters;

use App\Enums\PriceListProviderEnum;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

readonly class NichePerfumeUsdConverter implements ConverterInterface
{
    private const int INDEX_ARTICLE = 1;
    private const int INDEX_TITLE = 2;
    private const int INDEX_PRICE = 3;
    private const int FIRST_ROW = 14;

    public function convert(Spreadsheet $spreadsheet, string $firstColumnValue): array
    {
        $data = [];
        $activeSheet = $spreadsheet->getActiveSheet();
        $highestRow = $activeSheet->getHighestRow();
        $rows = $activeSheet->rangeToArray(sprintf("A%d:D%d", self::FIRST_ROW, $highestRow));
        foreach ($rows as $r) {
            if (empty($r[self::INDEX_ARTICLE]) || empty($r[self::INDEX_TITLE])) {
                continue;
            }
            $data[] = [
                $firstColumnValue,
                trim($r[self::INDEX_ARTICLE]),
                trim($r[self::INDEX_TITLE]),
                trim($r[self::INDEX_PRICE]),
            ];
        }
        return $data;
    }
}