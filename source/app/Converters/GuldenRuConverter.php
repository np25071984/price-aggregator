<?php

namespace App\Converters;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Entities\RawPriceListItem;
use App\Enums\PriceListProviderEnum;

readonly class GuldenRuConverter extends AbstractConverter
{
    private const int INDEX_ARTICLE = 1;
    private const int INDEX_TITLE = 3;
    private const int INDEX_PRICE = 4;
    private const int FIRST_ROW = 11;

    public function getPriceId(): PriceListProviderEnum
    {
        return PriceListProviderEnum::GuldenRu;
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
        $rows = $activeSheet->rangeToArray(sprintf("D%d:I%d", self::FIRST_ROW, $highestRow));
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