<?php

namespace App\Converters;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Entities\RawPriceListItem;

readonly class BeliyUsdConverter extends AbstractConverter
{
    private const int INDEX_ARTICLE = 0;
    private const int INDEX_TITLE = 1;
    private const int INDEX_PRICE = 2;
    private const int FIRST_ROW = 15;

    protected function getMarginPercent(): float
    {
        return 7.0;
    }

    protected function getFixes(): array
    {
        return [
            preg_quote("e.l.set(", "/") => "e.l. set(",
            preg_quote("kenzo\"ca", "/") => "kenzo \"ca",
            "eternelle100ml" => "eternelle 100ml",
            "pourpre100ml" => "pourpre 100ml",
            "stilll100ml" => "still 100ml",
            preg_quote("30ml`без", "/") => "30ml без",
            preg_quote(" mana`22 ", "/") => " mana `22 ",
            preg_quote(" animalique`23 ", "/") => " animalique `23 ",
            preg_quote(" scandal'20 ", "/") => " scandal '20 ",
            preg_quote(" black'19 ", "/") => " black '19 ",
            "cherry100ml" => "cherry 100ml",
            " 75 edt$" => " 75ml edt",
            "^eau de grey flannel " => "geoffrey beene eau de grey flannel ",
            "^hermessence vetiver tonka " => "hermes hermessence vetiver tonka ",
            "^comme des garcons-2 " => "comme des garcons ",
            "^cigar " => "cigar cigar ",
        ];
    }

    public function convert(Spreadsheet $spreadsheet): array
    {
        $data = [];
        $activeSheet = $spreadsheet->getActiveSheet();
        $highestRow = $activeSheet->getHighestRow();
        $rows = $activeSheet->rangeToArray(sprintf("A%d:C%d", self::FIRST_ROW, $highestRow));
        foreach ($rows as $r) {
            $title = $this->normolizeString($r[self::INDEX_TITLE]);
            $price = $this->getPriceWithMargin((float)trim($r[self::INDEX_PRICE]));
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