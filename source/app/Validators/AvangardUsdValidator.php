<?php

namespace App\Validators;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

final class AvangardUsdValidator
{
    public function __invoke(Spreadsheet $spreadsheet): bool
    {
        $sheet = $spreadsheet->getActiveSheet();
        if ($sheet->getCell('C3')->getValue() !== "Прайс-лист") {
            return false;
        }
        if ($sheet->getCell('E3')->getValue() !== "Итого:") {
            return false;
        }
        if ($sheet->getCell('B6')->getValue() !== "Код") {
            return false;
        }
        if ($sheet->getCell('C6')->getValue() !== "Наименование товаров") {
            return false;
        }
        if ($sheet->getCell('D6')->getValue() !== "Цена") {
            return false;
        }
        if ($sheet->getCell('E6')->getValue() !== "Заказ") {
            return false;
        }
        if ($sheet->getCell('F6')->getValue() !== "Сумма") {
            return false;
        }

        return true;
    }
}