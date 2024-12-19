<?php

namespace App\Validators;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

final class FestivalRubValidator
{
    public function __invoke(Spreadsheet $spreadsheet): bool
    {
        $sheet = $spreadsheet->getActiveSheet();
        if (mb_substr($sheet->getCell('D1')->getValue(), 0, mb_strlen("Прайс-лист")) !== "Прайс-лист") {
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
        if ($sheet->getCell('J6')->getValue() !== "Заказ") {
            return false;
        }
        if ($sheet->getCell('K6')->getValue() !== "Сумма") {
            return false;
        }
        if ($spreadsheet->getSheetNames() !== ["Косметика и уход", "Парфюмерия"]) {
            return false;
        }

        return true;
    }
}