<?php

namespace App\Converters;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Entities\RawPriceListItem;

readonly class ZurabUsdConverter extends AbstractConverter
{
    private const int INDEX_ARTICLE = 0;
    private const int INDEX_TITLE = 1;
    private const int INDEX_PRICE = 3;
    private const int FIRST_ROW = 4;

    protected function getFixes(): array
    {
        return [
            " edt 100m w tester$" => " edt 100ml w tester",
            " 50mll w tester$" => " 50ml w tester",
            "edp100ml" => "edp 100ml",
            preg_quote(" parfum1.5ml ", "/") => " parfum 1.5ml ",
            " parfum100ml$" => " parfum 100ml",
            " edt50ml$" => " edt 50ml",
            " 100mlt tester$" => " 100ml tester",
            "edt 50m б/спр$" => "edt 50ml б/спр",
            " edp15ml$" => " edp 15ml",
            " edp 1m$" => " edp 1ml",
        ];
    }

    public function convert(Spreadsheet $spreadsheet): array
    {
        $data = [];
        $activeSheet = $spreadsheet->getActiveSheet();
        $highestRow = $activeSheet->getHighestRow();
        $rows = $activeSheet->rangeToArray(sprintf("A%d:D%d", self::FIRST_ROW, $highestRow));
        $currentBrand = null;
        foreach ($rows as $r) {
            if (empty($r[self::INDEX_ARTICLE])) {
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
                originalTitle: $r[self::INDEX_TITLE],
                normalizedTitle: $title,
                price: $price,
            );
        }
        return $data;
    }
}