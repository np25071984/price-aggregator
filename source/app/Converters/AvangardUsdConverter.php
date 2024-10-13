<?php

namespace App\Converters;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Entities\RawPriceListItem;

readonly class AvangardUsdConverter extends AbstractConverter
{
    private const int INDEX_ARTICLE = 0;
    private const int INDEX_TITLE = 1;
    private const int INDEX_PRICE = 2;
    private const int FIRST_ROW = 7;

    public function convert(Spreadsheet $spreadsheet): array
    {
        $data = [];
        $activeSheet = $spreadsheet->getActiveSheet();
        $highestRow = $activeSheet->getHighestRow();
        $rows = $activeSheet->rangeToArray(sprintf("B%d:D%d", self::FIRST_ROW, $highestRow));
        $currentBrand = null;
        foreach ($rows as $i => $r) {
            if (empty($r[self::INDEX_ARTICLE])) {
                if (empty($r[self::INDEX_TITLE])) {
                    // the last row is empty row
                    continue;
                }
                $currentBrand = $this->normolizeString($r[self::INDEX_TITLE]);
                continue;
            }
            $title = $this->normolizeString($r[self::INDEX_TITLE]);
            if (mb_substr($title, 0, mb_strlen($currentBrand)) !== $currentBrand) {
                $title = $currentBrand . " " . $title;
            }
            $price = (float)trim($r[self::INDEX_PRICE]);
            $data[] = new RawPriceListItem(
                article: trim($r[self::INDEX_ARTICLE]),
                title: $title,
                price: $price,
            );
        }
        return $data;
    }
}