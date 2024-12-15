<?php

namespace App\Converters;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Entities\RawPriceListItem;
use App\Enums\PriceListProviderEnum;

readonly class NashaFirmaUsdConverter extends AbstractConverter
{
    private const int INDEX_ARTICLE = 0;
    private const int INDEX_TITLE = 1;
    private const int INDEX_PRICE = 3;
    private const int FIRST_ROW = 7;

    public function getPriceId(): PriceListProviderEnum
    {
        return PriceListProviderEnum::NashaFirmaUsd;
    }

    protected function getMarginPercent(): float
    {
        return 7.0;
    }

    protected function getFixes(): array
    {
        return [
            " 2[,\.]5 edp" => " 2,5ml edp",
            " 7[,\.]5 edp" => " 7,5ml edp",
            " 15 ed([pt])" => " 15ml ed\\1",
            " 30 edp" => " 30ml edp",
            " 30 parfum$" => " 30ml parfum",
            " 35 parfum$" => " 35ml parfum",
            " 50 extrait de parfum" => " 50ml extrait de parfum",
            " 75 parfum$" => " 75ml parfum",
            " 75 edp$" => " 75ml edp",
            " 90 edp" => " 90ml edp",
            " 50 ed([pc])" => " 50ml ed\\1",
            " 100 ed([tc])" => " 100ml ed\\1",
            " 125 edt$" => " 125ml edt",
            " 200 ed([tp])" => " 200ml ed\\1",
            " ([wm])7,5ml edp$" => " \\1 7,5ml edp",
            " sky7,5ml edp$" => " sky 7,5ml edp",
            " m 7.5 edp$" => " m 7,5ml edp",
            " (chic|fever|venus|gate|intrigue|code|iceberg|sunmusk|power|dancer)7,5ml edp$" => " \\1 7,5ml edp",
            " 100 (edp|perfume)$" => " 100ml \\1",
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