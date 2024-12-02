<?php

namespace App\Validators;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

final class GuldenRuValidator
{
    public function __invoke(Spreadsheet $spreadsheet): bool
    {
        $sheet = $spreadsheet->getActiveSheet();
        if ($sheet->getCell('D1')->getValue() !== "Прайс-лист") {
            return false;
        }
        if (mb_substr($sheet->getCell('D4')->getValue(), 0, mb_strlen("Цены указаны на")) !== "Цены указаны на") {
            return false;
        }
        if ($sheet->getCell('D6')->getValue() !== "Бренд") {
            return false;
        }
        if ($sheet->getCell('E6')->getValue() !== "Артикул") {
            return false;
        }
        if ($sheet->getCell('G6')->getValue() !== "Номенклатура") {
            return false;
        }
        if ($sheet->getCell('H6')->getValue() !== "Цена") {
            return false;
        }

        if ($spreadsheet->getSheetNames() !== ["Косметика и уход", "Парфюмерия"]) {
            return false;
        }

        return true;
    }
}