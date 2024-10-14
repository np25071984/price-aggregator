<?php

namespace App\Converters;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Entities\RawPriceListItem;

readonly class BeliyUsdConverter extends AbstractConverter
{
    private const int INDEX_ARTICLE = 0;
    private const int INDEX_TITLE = 1;
    private const int INDEX_PRICE = 2;
    private const int FIRST_ROW = 15;

    public function convert(Spreadsheet $spreadsheet): array
    {
        $data = [];
        $activeSheet = $spreadsheet->getActiveSheet();
        $highestRow = $activeSheet->getHighestRow();
        $rows = $activeSheet->rangeToArray(sprintf("A%d:C%d", self::FIRST_ROW, $highestRow));
        foreach ($rows as $r) {
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
        $string = str_replace("e.l.set(", "e.l. set(", $string);
        $string = str_replace("kenzo\"ca", "kenzo \"ca", $string);

        return $string;
    }
}