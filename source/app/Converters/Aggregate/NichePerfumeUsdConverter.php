<?php

namespace App\Converters\Aggregate;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Entities\RawPriceListItem;
use App\Enums\PriceListProviderEnum;

readonly class NichePerfumeUsdConverter extends AbstractConverter
{
    private const int INDEX_ARTICLE = 1;
    private const int INDEX_TITLE = 2;
    private const int INDEX_PRICE = 3;
    private const int FIRST_ROW = 14;

    public function getPriceId(): PriceListProviderEnum
    {
        return PriceListProviderEnum::NichePerfumeUsd;
    }

    protected function getFixes(): array
    {
        return [
            " mltest" => " ml test",
            " edp 50$" => " edp 50ml",
        ];
    }

    protected function getMarginPercent(): float
    {
        return 7.0;
    }

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
            $price = str_replace(" USD", "", $r[self::INDEX_PRICE]); // rangeToArray returns currency
            $price = $this->getPriceWithMargin((float)trim($price));
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