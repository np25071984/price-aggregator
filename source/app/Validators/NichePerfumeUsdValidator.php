<?php

namespace App\Validators;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

final class NichePerfumeUsdValidator
{
    public function __invoke(Spreadsheet $spreadsheet): bool
    {
        $sheet = $spreadsheet->getActiveSheet();
        if ($sheet->getCell('B1')->getValue() !== "Прайс-лист") {
            return false;
        }
        if ($sheet->getCell('B3')->getValue() !== "NICHE-PERFUME") {
            return false;
        }
        if ($sheet->getCell('B5')->getValue() !== "В валютах цен.") {
            return false;
        }
        if (mb_substr($sheet->getCell('B6')->getValue(), 0, mb_strlen("Цены указаны на")) !== "Цены указаны на") {
            return false;
        }
        if ($sheet->getCell('B9')->getValue() !== "Номенклатура.Артикул ") {
            return false;
        }
        if ($sheet->getCell('C9')->getValue() !== "Ценовая группа/ Номенклатура/ Характеристика номенклатуры") {
            return false;
        }
        if ($sheet->getCell('D9')->getValue() !== "Оптовая цена USD МСК") {
            return false;
        }

        return true;
    }
}