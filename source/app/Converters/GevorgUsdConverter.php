<?php

namespace App\Converters;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Entities\RawPriceListItem;

readonly class GevorgUsdConverter extends AbstractConverter
{
    private const int INDEX_ARTICLE = 0;
    private const int INDEX_TITLE = 5;
    private const int INDEX_PRICE = 13;
    private const int FIRST_ROW = 3;

    protected function getMarginPercent(): float
    {
        return 7.0;
    }

    protected function getFixes(): array
    {
        return [
            preg_quote("100ml(в", "/") =>"100ml (в",
            preg_quote("50ml(без)", "/") => "50ml (без",
            preg_quote("50ml(в", "/") => "50ml (в",
            "parfum120ml" => "parfum 120ml",
            preg_quote("10m(в", "/") => "10ml (в",
            preg_quote("10mll(в", "/") => "10ml (в",
            preg_quote("10ml(в", "/") => "10ml (в",
            preg_quote("10ml(оригинал", "/") => "10ml (оригинал",
            preg_quote("10ml(отливант)", "/") => "10ml (отливант)",
            preg_quote("50ml(без", "/") => "50ml (без",
            "roses on ice edp 50$" => "roses on ice edp 50ml",
            preg_quote("parfum 5ml(отливант)", "/") . "$" => "parfum 5ml (отливант)",
            "^alexandre j.the " => "alexandre j. the ",
        ];
    }

    public function convert(Spreadsheet $spreadsheet): array
    {
        $data = [];
        $activeSheet = $spreadsheet->getActiveSheet();
        $highestRow = $activeSheet->getHighestRow();
        $rows = $activeSheet->rangeToArray(sprintf("A%d:N%d", self::FIRST_ROW, $highestRow));
        foreach ($rows as $r) {
            if (empty($r[self::INDEX_ARTICLE])) {
                continue;
            }
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