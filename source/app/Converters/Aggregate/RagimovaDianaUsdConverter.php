<?php

namespace App\Converters\Aggregate;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Entities\RawPriceListItem;
use App\Enums\PriceListProviderEnum;

readonly class RagimovaDianaUsdConverter extends AbstractConverter
{
    private const int INDEX_ARTICLE = 0;
    private const int INDEX_TITLE = 1;
    private const int INDEX_PRICE = 2;
    private const int FIRST_ROW = 5;

    public function getPriceId(): PriceListProviderEnum
    {
        return PriceListProviderEnum::RagimovaDianaUsd;
    }

    protected function getMarginPercent(): float
    {
        return 7.0;
    }

    protected function getFixes(): array
    {
        return [
            " parfum100ml " => " parfum 100ml ",
            " 20m пр. франция$" => " 20ml пр. франция",
            preg_quote(" edp100ml", "/") => " edp 100ml",
        ];
    }

    public function convert(Spreadsheet $spreadsheet): array
    {
        $data = [];
        $activeSheet = $spreadsheet->getActiveSheet();
        $highestRow = $activeSheet->getHighestRow();
        $rows = $activeSheet->rangeToArray(sprintf("A%d:C%d", self::FIRST_ROW, $highestRow));
        foreach ($rows as $r) {
            if (empty($r[self::INDEX_ARTICLE])) {
                continue;
            }
            $price = $this->getPriceWithMargin((float)trim($r[self::INDEX_PRICE]));
            $data[] = new RawPriceListItem(
                article: trim($r[self::INDEX_ARTICLE]),
                originalTitle: $r[self::INDEX_TITLE],
                normalizedTitle: $this->normolizeString($r[self::INDEX_TITLE]),
                price: $price,
            );
        }
        return $data;
    }
}