<?php

namespace App\Converters;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Entities\RawPriceListItem;

readonly class KurzinaUsdConverter extends AbstractConverter
{
    private const int INDEX_ARTICLE = 0;
    private const int INDEX_TITLE = 1;
    private const int INDEX_PRICE = 3;
    private const int FIRST_ROW = 2;

    public function convert(Spreadsheet $spreadsheet): array
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
            $price = (float)trim($r[self::INDEX_PRICE]);
            $data[] = new RawPriceListItem(
                article: trim($r[self::INDEX_ARTICLE]),
                title: $title,
                price: $price,
            );
        }
        return $data;
    }

    private function fixData(string $string): string
    {
        return str_replace("ml отливант5", "5ml отливант", $string);
    }
}