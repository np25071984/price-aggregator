<?php

namespace App\Converters;

use App\Enums\PriceListProviderEnum;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

readonly class PriceListConverter implements ConverterInterface
{
    public function __construct(
        public PriceListProviderEnum $id,
        public int $indexArticle,
        public int $indexTitle,
        public int $indexPrice,
        public int $firstRow,
    ) {
    }

    public function convert(Spreadsheet $spreadsheet, string $firstColumnValue): array
    {
        $data = [];
        $activeSheet = $spreadsheet->getActiveSheet();
        $highestRow = $activeSheet->getHighestRow();
        $rows = $activeSheet->rangeToArray("A{$this->firstRow}:F{$highestRow}");
        foreach ($rows as $r) {
            if (empty($r[$this->indexArticle]) || empty($r[$this->indexTitle]) || $r[$this->indexArticle] === "НФ-00001873") {
                continue;
            }
            $data[] = [
                $firstColumnValue,
                trim($r[$this->indexArticle]),
                trim($r[$this->indexTitle]),
                trim($r[$this->indexPrice]),
            ];
        }
        return $data;
    }
}