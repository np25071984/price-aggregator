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
            " lys 10 edp " => " lys 10ml edp ",
        ];
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
            $price = (float)trim($r[self::INDEX_PRICE]);
            $title = $this->normolizeString($r[self::INDEX_TITLE]);
            $data[] = new RawPriceListItem(
                article: trim($r[self::INDEX_ARTICLE]),
                title: $title,
                price: $price,
            );
        }
        return $data;
    }
}