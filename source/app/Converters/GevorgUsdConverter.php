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
        $rows = $activeSheet->rangeToArray(sprintf("A%d:N%d", self::FIRST_ROW, $highestRow));
        foreach ($rows as $r) {
            if (empty($r[self::INDEX_ARTICLE])) {
                continue;
            }
            $title = $this->normolizeString($r[self::INDEX_TITLE]);
            $title = $this->fixData($title);
            $price = (float)trim($r[self::INDEX_PRICE]);
            $data[] = new RawPriceListItem(
                article: trim($r[self::INDEX_ARTICLE]),
                title: $this->normolizeString($r[self::INDEX_TITLE]),
                price: $price,
            );
        }
        return $data;
    }

    private function fixData(string $string): string
    {
        $string = str_replace("100ml(в", "100ml (в", $string);
        $string = str_replace("50ml(без", "50ml (без", $string);
        $string = str_replace("50ml(в", "50ml (в", $string);
        $string = str_replace("parfum120ml", "parfum 120ml", $string);
        $string = str_replace("10m(в", "10ml (в", $string);
        $string = str_replace("10mll(в", "10ml (в", $string);
        $string = str_replace("10ml(в", "10ml (в", $string);
        $string = str_replace("5ml(отливант)", "5ml (отливант)", $string);

        return $string;
    }
}