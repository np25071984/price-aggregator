<?php

namespace App\Converters;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Entities\RawPriceListItem;

readonly class DePerfumesConverter extends AbstractConverter
{
    private const int INDEX_ARTICLE = 0;
    private const int INDEX_TITLE = 1;
    private const int INDEX_PRICE = 2;
    private const int FIRST_ROW = 4;

    protected function getFixes(): array
    {
        return [
            preg_quote("1la(парфюмированное моющее средство для стирки)", "/") => "1la (парфюмированное моющее средство для стирки)",
            preg_quote("1l(парфюмированное моющее средство для стирки)", "/") => "1l (парфюмированное моющее средство для стирки)",
            preg_quote("dorin-un air", "/") => "dorin - un air",
            preg_quote(" lys 10 edp ", "/") => " lys 10ml edp ",
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
        $rows = $activeSheet->rangeToArray(sprintf("B%d:D%d", self::FIRST_ROW, $highestRow));
        foreach ($rows as $r) {
            if (empty($r[self::INDEX_ARTICLE])) {
                continue;
            }
            $price = $this->getPriceWithMargin((float)trim($r[self::INDEX_PRICE]));
            $title = $this->normolizeString($r[self::INDEX_TITLE]);
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