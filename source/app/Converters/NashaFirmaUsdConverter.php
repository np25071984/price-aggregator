<?php

namespace App\Converters;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Entities\RawPriceListItem;

readonly class NashaFirmaUsdConverter extends AbstractConverter
{
    private const int INDEX_ARTICLE = 0;
    private const int INDEX_TITLE = 1;
    private const int INDEX_PRICE = 3;
    private const int FIRST_ROW = 7;

    protected function getFixes(): array
    {
        return [
            " 7,5 edp" => "7,5ml edp",
            " 15 ed([pt])" => " 15ml ed\\1",
            " 30 edp" => " 30ml edp",
            " 50 ed([pc])" => " 50ml ed\\1",
            " 75 edp" => " 75ml edp",
            " 100 ed([tc])" => " 100ml ed\\1",
            " 200 edt" => " 200ml edt",
        ];
    }

    public function convert(Spreadsheet $spreadsheet): array
    {
        $data = [];
        $activeSheet = $spreadsheet->getActiveSheet();
        $highestRow = $activeSheet->getHighestRow();
        $rows = $activeSheet->rangeToArray(sprintf("B%d:E%d", self::FIRST_ROW, $highestRow));
        foreach ($rows as $r) {
            if (empty($r[self::INDEX_TITLE])) {
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