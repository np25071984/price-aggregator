<?php

namespace App\Converters;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

readonly class PricePRCUsdConverter extends AbstractConverter
{
    private const int INDEX_ARTICLE = 0;
    private const int INDEX_TITLE = 1;
    private const int INDEX_PRICE = 3;
    private const int FIRST_ROW = 2;

    public function convert(Spreadsheet $spreadsheet, string $firstColumnValue): array
    {
        $data = [];
        $activeSheet = $spreadsheet->getActiveSheet();
        $highestRow = $activeSheet->getHighestRow();
        $rows = $activeSheet->rangeToArray(sprintf("B%d:E%d", self::FIRST_ROW, $highestRow));
        foreach ($rows as $r) {
            // we don' know what НФ-00001873 item means
            if (empty($r[self::INDEX_TITLE]) || ($r[self::INDEX_TITLE] === "НФ-00001873")) {
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