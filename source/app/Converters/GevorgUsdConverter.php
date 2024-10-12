<?php

namespace App\Converters;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Entities\RawPriceListItem;

readonly class GevorgUsdConverter extends AbstractConverter
{
    private const int INDEX_ARTICLE = 0;
    private const int INDEX_TITLE = 5;
    private const int INDEX_PRICE = 13;
    private const int FIRST_ROW = 3;

    public function convert(Spreadsheet $spreadsheet): array
    {
        $data = [];
        $activeSheet = $spreadsheet->getActiveSheet();
        $highestRow = $activeSheet->getHighestRow();
        $rows = $activeSheet->rangeToArray(sprintf("B%d:N%d", self::FIRST_ROW, $highestRow));
        foreach ($rows as $r) {
            if (empty($r[self::INDEX_ARTICLE])) {
                continue;
            }
            $price = (float)trim($r[self::INDEX_PRICE]);
            $data[] = new RawPriceListItem(
                article: trim($r[self::INDEX_ARTICLE]),
                title: $this->normolizeString($r[self::INDEX_TITLE]),
                price: $price,
            );
        }
        return $data;
    }
}