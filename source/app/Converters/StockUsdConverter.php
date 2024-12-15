<?php

namespace App\Converters;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Entities\RawPriceListItem;
use App\Enums\PriceListProviderEnum;

readonly class StockUsdConverter extends AbstractConverter
{
    private const int INDEX_ARTICLE = 0;
    private const int INDEX_TITLE = 1;
    private const int INDEX_PRICE = 2;
    private const int FIRST_ROW = 4;

    public function getPriceId(): PriceListProviderEnum
    {
        return PriceListProviderEnum::StockUsd;
    }

    protected function getFixes(): array
    {
        return [];
    }

    public function convert(Spreadsheet $spreadsheet): array
    {
        $data = [];
        $activeSheet = $spreadsheet->getActiveSheet();
        $highestRow = $activeSheet->getHighestRow();
        $rows = $activeSheet->rangeToArray(sprintf("A%d:C%d", self::FIRST_ROW, $highestRow));
        foreach ($rows as $r) {
            if (empty($r[self::INDEX_ARTICLE]) || empty($r[self::INDEX_TITLE])) {
                continue;
            }
            $title = $this->normolizeString($r[self::INDEX_TITLE]);
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