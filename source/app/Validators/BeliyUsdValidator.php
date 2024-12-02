<?php

namespace App\Validators;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

final class BeliyUsdValidator
{
    public function __invoke(Spreadsheet $spreadsheet): bool
    {
        $sheet = $spreadsheet->getActiveSheet();
        if (mb_strpos($sheet->getCell('A1')->getValue(), "Прайс от") === false) {
            return false;
        }
        if ($sheet->getCell('A2')->getValue() !== "Не изменяйте форму прайс-листа, пожалуйста, только проставьте кол-ва.") {
            return false;
        }
        if ($sheet->getCell('A3')->getValue() !== "1. Курс уточняйте на день оплаты! ") {
            return false;
        }
        if ($sheet->getCell('A4')->getValue() !== "2. Прием заказов, обработка и доставка осуществляется в будние дни.") {
            return false;
        }
        if ($sheet->getCell('A5')->getValue() !== "3. Заказ, отправленный до 13-00 привозим в этот же день (только Москва), высланные после 13-00 привозим на следующий день.") {
            return false;
        }
        if ($sheet->getCell('A10')->getValue() !== "Дата планируемой отгрузки :") {
            return false;
        }
        if ($sheet->getCell('A11')->getValue() !== "ВАЖНО: Цены в прайс-листе актуальны только в день рассылки!!!") {
            return false;
        }
        if ($sheet->getCell('A14')->getValue() !== "КОД") {
            return false;
        }
        if ($sheet->getCell('B14')->getValue() !== "НАИМЕНОВАНИЕ ТОВАРА") {
            return false;
        }
        if ($sheet->getCell('C14')->getValue() !== "ЦЕНА") {
            return false;
        }
        if ($spreadsheet->getSheetNames() !== ["price"]) {
            return false;
        }

        return true;
    }
}