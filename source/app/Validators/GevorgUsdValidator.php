<?php

namespace App\Validators;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

final class GevorgUsdValidator
{
    public function __invoke(Spreadsheet $spreadsheet): bool
    {
        $sheet = $spreadsheet->getActiveSheet();
        if (mb_strpos($sheet->getCell('A1')->getValue(), "Прайс-лист на") === false) {
            return false;
        }
        if (!is_null($sheet->getCell('A2')->getValue())) {
            return false;
        }
        if (!is_null($sheet->getCell('D2')->getValue())) {
            return false;
        }
        if (!is_null($sheet->getCell('C2')->getValue())) {
            return false;
        }
        if (!is_null($sheet->getCell('D2')->getValue())) {
            return false;
        }
        if (!is_null($sheet->getCell('E2')->getValue())) {
            return false;
        }
        if (!is_null($sheet->getCell('G2')->getValue())) {
            return false;
        }
        if ($spreadsheet->getSheetNames() !== ["TDSheet"]) {
            return false;
        }

        return true;
    }
}