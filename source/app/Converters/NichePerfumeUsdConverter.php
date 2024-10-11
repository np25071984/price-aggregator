<?php

namespace App\Converters;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

readonly class NichePerfumeUsdConverter extends AbstractConverter
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

            $title = $this->normolizeString($r[self::INDEX_TITLE]);
            $title = $this->fixData($title);

            $data[] = [
                $firstColumnValue,
                trim($r[self::INDEX_ARTICLE]),
                $title,
                trim($r[self::INDEX_PRICE]),
            ];
        }
        return $data;
    }

    private function fixData(string $string): string
    {
        return str_replace(" mltest", " ml test", $string);
    }
}