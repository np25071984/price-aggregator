<?php

namespace App\Converters\Merge;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Enums\PriceListProviderEnum;

readonly class KurzinaRusConverter
{
    private const int INDEX_ARTICLE = 0;
    private const int INDEX_TITLE = 1;
    private const int INDEX_PRICE = 2;

    public function getPriceId(): PriceListProviderEnum
    {
        return PriceListProviderEnum::KurzinaUsd;
    }

    public function convert(Spreadsheet $spreadsheet): array
    {
        $data = [];
        $rows = $spreadsheet->getActiveSheet()->toArray();
        foreach ($rows as $i => $r) {
            if (in_array($r[self::INDEX_ARTICLE], ["", "Артикул"])) {
                $currentBrand = $r[self::INDEX_TITLE];
                continue;
            }
            $articl = str_replace([";"], [""], $r[self::INDEX_ARTICLE]);
            $name = str_replace([";"], [""], $r[self::INDEX_TITLE]);
            $price = str_replace([","], [""], $r[self::INDEX_PRICE]);
            $data[$currentBrand][] = [$articl, $name, $price];
        }

        return $data;
    }

}