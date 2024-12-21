<?php

namespace App\Converters\Merge;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Enums\PriceListProviderEnum;

readonly class FestivalRubConverter
{
    private const int INDEX_BRAND = 0;
    private const int INDEX_ARTICLE = 1;
    private const int INDEX_TITLE = 3;
    private const int INDEX_PRICE = 4;

    public function getPriceId(): PriceListProviderEnum
    {
        return PriceListProviderEnum::FestivalRub;
    }

    public function convert(Spreadsheet $spreadsheet): array
    {
        $data = [];
        $activeSheet = $spreadsheet->getActiveSheet();
        $highestRow = $activeSheet->getHighestRow();
        $rows = $activeSheet->rangeToArray(sprintf("D8:H%d", $highestRow));
        foreach ($rows as $r) {
            if (empty($r[self::INDEX_TITLE])) {
                continue;
            }
            $brand = $r[self::INDEX_BRAND];
            $articl = $r[self::INDEX_ARTICLE];
            $name = $r[self::INDEX_TITLE];
            $price = str_replace([",", ' '], "", $r[self::INDEX_PRICE]);
            $price = ceil($price * 1.07);
            $data[$brand][] = [$articl, $name, $price];
        }

        return $data;
    }
}