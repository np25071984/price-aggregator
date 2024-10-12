<?php

namespace App\Converters;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

readonly class GuldenRuConverter extends AbstractConverter
{
    private const int INDEX_ARTICLE = 1;
    private const int INDEX_TITLE = 3;
    private const int INDEX_PRICE = 4;
    private const int FIRST_ROW = 11;

    public function convert(Spreadsheet $spreadsheet, string $firstColumnValue): array
    {
        $data = [];
        $activeSheet = $spreadsheet->getActiveSheet();
        $highestRow = $activeSheet->getHighestRow();
        $rows = $activeSheet->rangeToArray(sprintf("D%d:I%d", self::FIRST_ROW, $highestRow));
        foreach ($rows as $r) {
            if (empty($r[self::INDEX_ARTICLE])) {
                continue;
            }
            $data[] = [
                $firstColumnValue,
                trim($r[self::INDEX_ARTICLE]),
                $this->normolizeString($r[self::INDEX_TITLE]),
                trim($r[self::INDEX_PRICE]),
            ];
        }
        return $data;
    }
}