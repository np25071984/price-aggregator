<?php

namespace App\Validators;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

final class ZurabUsdValidator
{
    public function __invoke(Spreadsheet $spreadsheet): bool
    {
        $sheet = $spreadsheet->getActiveSheet();
        if ($sheet->getCell('A1')->getValue() !== "Прайс Раритет. +7(985)363-99-11") {
            return false;
        }
        if ($sheet->getCell('A2')->getValue() !== "Код") {
            return false;
        }
        if ($sheet->getCell('B2')->getValue() !== "Наименование") {
            return false;
        }
        if ($sheet->getCell('D2')->getValue() !== "Цена") {
            return false;
        }
        if ($sheet->getCell('E2')->getValue() !== "Заказ") {
            return false;
        }
        if ($spreadsheet->getSheetNames() !== ["Лист1", "Лист2", "Лист3"]) {
            return false;
        }

        return true;
    }
}