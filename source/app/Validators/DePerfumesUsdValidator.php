<?php

namespace App\Validators;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

final class DePerfumesUsdValidator
{
    public function __invoke(Spreadsheet $spreadsheet): bool
    {
        $sheet = $spreadsheet->getActiveSheet();
        if (mb_strpos($sheet->getCell('B1')->getValue(), "De-Perfumes") === false) {
            return false;
        }
        if (mb_strpos($sheet->getCell('B1')->getValue(), "ТЦ Гульден  пав. 2148") === false) {
            return false;
        }
        if (mb_strpos($sheet->getCell('B1')->getValue(), "+7 (985) 477 09 09") === false) {
            return false;
        }
        if (mb_strpos($sheet->getCell('B1')->getValue(), "deparfum16@yandex.ru") === false) {
            return false;
        }
        if ($sheet->getCell('C2')->getValue() !== "МАРКИРОВАННЫЙ ПРАЙС (возможность передачи по ЭДО)") {
            return false;
        }
        if ($sheet->getCell('B3')->getValue() !== "код") {
            return false;
        }
        if ($sheet->getCell('C3')->getValue() !== "Наименование") {
            return false;
        }
        if ($sheet->getCell('D3')->getValue() !== "цена") {
            return false;
        }
        if ($sheet->getCell('E3')->getValue() !== "заказ") {
            return false;
        }
        if ($spreadsheet->getSheetNames() !== ["прайс"]) {
            return false;
        }

        return true;
    }
}