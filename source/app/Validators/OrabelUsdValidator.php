<?php

namespace App\Validators;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

final class OrabelUsdValidator
{
    public function __invoke(Spreadsheet $spreadsheet): bool
    {
        $sheet = $spreadsheet->getActiveSheet();
        if ($sheet->getCell('A2')->getValue() !== "Код") {
            return false;
        }
        if ($sheet->getCell('B2')->getValue() !== "Наименование") {
            return false;
        }
        if ($sheet->getCell('C2')->getValue() !== "Цена") {
            return false;
        }
        if ($sheet->getCell('D2')->getValue() !== "Заказ") {
            return false;
        }
        if ($spreadsheet->getSheetNames() !== ["Лист1"]) {
            return false;
        }

        return true;
    }
}