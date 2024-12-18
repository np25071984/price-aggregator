<?php

namespace App\Validators;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

final class KurzinaRusValidator
{
    public function __invoke(Spreadsheet $spreadsheet): bool
    {
        $sheet = $spreadsheet->getActiveSheet();
        if ($sheet->getCell('A1')->getValue() !== "Артикул") {
            return false;
        }
        if ($sheet->getCell('B1')->getValue() !== "Наименование") {
            return false;
        }
        if ($sheet->getCell('C1')->getValue() !== "Прайс") {
            return false;
        }
        if ($sheet->getCell('D1')->getValue() !== "Заказ") {
            return false;
        }
        if ($spreadsheet->getSheetNames() !== ["Лист_1"]) {
            return false;
        }

        return true;
    }
}