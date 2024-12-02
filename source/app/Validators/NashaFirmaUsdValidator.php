<?php

namespace App\Validators;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

final class NashaFirmaUsdValidator
{
    public function __invoke(Spreadsheet $spreadsheet): bool
    {
        $sheet = $spreadsheet->getActiveSheet();
        if ($sheet->getCell('B2')->getValue() !== "111") {
            return false;
        }
        if ($sheet->getCell('B4')->getValue() !== "Код") {
            return false;
        }
        if ($sheet->getCell('C4')->getValue() !== "Полное наименование") {
            return false;
        }
        if ($sheet->getCell('D4')->getValue() !== "Оптовая цена") {
            return false;
        }
        if ($spreadsheet->getSheetNames() !== ["TDSheet"]) {
            return false;
        }

        return true;
    }
}