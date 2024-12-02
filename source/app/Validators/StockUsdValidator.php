<?php

namespace App\Validators;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

final class StockUsdValidator
{
    public function __invoke(Spreadsheet $spreadsheet): bool
    {
        $sheet = $spreadsheet->getActiveSheet();
        if ($sheet->getCell('A1')->getValue() !== "ПАРФЮМ СТОК ") {
            return false;
        }
        if ($sheet->getCell('B2')->getValue() !== "НАИМЕНОВАНИЕ") {
            return false;
        }
        if ($sheet->getCell('C2')->getValue() !== "Прайс-лист") {
            return false;
        }
        if ($sheet->getCell('C3')->getValue() !== "Цена") {
            return false;
        }
        if ($sheet->getCell('D3')->getValue() !== "Заказ") {
            return false;
        }
        if ($spreadsheet->getSheetNames() !== ["TDSheet"]) {
            return false;
        }

        return true;
    }
}