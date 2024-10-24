<?php

namespace App\Converters;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Entities\RawPriceListItem;

readonly class KurzinaUsdConverter extends AbstractConverter
{
    private const int INDEX_ARTICLE = 0;
    private const int INDEX_TITLE = 1;
    private const int INDEX_PRICE = 2;
    private const int FIRST_ROW = 3;

    protected function getFixes(): array
    {
        return [
            "ml отливант5" => "5ml отливант",
            " edp100 ml tester$" => " edp 100ml tester",
            " parfum100 ml tester$" => " parfum 100ml tester",
            "^bespoke " => "keiko mecheri bespoke",
            "^edenfallsedp" => "m.micallef edenfalls edp",
            " mmmm...edp " => " mmmm... edp ",
        ];
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
                title: $title,
                price: $price,
            );
        }
        return $data;
    }

}