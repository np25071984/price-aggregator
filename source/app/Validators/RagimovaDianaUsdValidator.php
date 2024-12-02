<?php

namespace App\Validators;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

final class RagimovaDianaUsdValidator
{
    public function __invoke(Spreadsheet $spreadsheet): bool
    {
        $sheet = $spreadsheet->getActiveSheet();
        if ($sheet->getCell('A4')->getValue() !== "Код") {
            return false;
        }
        if ($sheet->getCell('B4')->getValue() !== "Наименование товара") {
            return false;
        }
        if ($sheet->getCell('C4')->getValue() !== "Опт.цена") {
            return false;
        }
        if ($spreadsheet->getSheetNames() !== ["Лист1"]) {
            return false;
        }

        return true;
    }
}