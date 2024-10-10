<?php

namespace App;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Enums\PriceListProviderEnum;

class PriceListIdentifier
{
    public function identiry(Spreadsheet $spreadsheet): PriceListProviderEnum
    {
        switch (true) {
            case $this->isNichePerfumeRuPriceList($spreadsheet):
                return PriceListProviderEnum::NichePerfumeRu;
            case $this->isNichePerfumeUsdPriceList($spreadsheet):
                return PriceListProviderEnum::NichePerfumeUsd;
            case $this->isKurzinaRuPriceList($spreadsheet):
                return PriceListProviderEnum::KurzinaRu;
            case $this->isAllScentUsdPriceList($spreadsheet):
                return PriceListProviderEnum::AllScentUsd;
            case $this->isBeautyParfumRuPriceList($spreadsheet):
                return PriceListProviderEnum::BeautyPerfumeRu;
            default:
                return PriceListProviderEnum::Unknown;
        }
    }

    private function isNichePerfumeUsdPriceList(Spreadsheet $spreadsheet): bool
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

    private function isNichePerfumeRuPriceList(Spreadsheet $spreadsheet): bool
    {
        $sheet = $spreadsheet->getActiveSheet();
        if ($sheet->getCell('B1')->getValue() !== "Прайс-лист") {
            return false;
        }
        if ($sheet->getCell('B5')->getValue() !== "В валютах цен.") {
            return false;
        }
        if (mb_substr($sheet->getCell('B6')->getValue(), 0, mb_strlen("Цены указаны на")) !== "Цены указаны на") {
            return false;
        }
        if ($sheet->getCell('B9')->getValue() !== "Код") {
            return false;
        }
        if ($sheet->getCell('C9')->getValue() !== "Ценовая группа/ Номенклатура/ Характеристика номенклатуры") {
            return false;
        }
        if ($sheet->getCell('D9')->getValue() !== "Оптовая") {
            return false;
        }

        return true;
    }

    private function isKurzinaRuPriceList(Spreadsheet $spreadsheet): bool
    {
        $sheet = $spreadsheet->getActiveSheet();
        if ($sheet->getCell('A1')->getValue() !== "артикул") {
            return false;
        }
        if ($sheet->getCell('B1')->getValue() !== "наименование") {
            return false;
        }
        if ($sheet->getCell('D1')->getValue() !== "цена") {
            return false;
        }
        if ($sheet->getCell('E1')->getValue() !== "заказ") {
            return false;
        }
        if ($spreadsheet->getSheetNames() == ["Лиcт2"]) {
            return false;
        }

        return true;
    }

    private function isAllScentUsdPriceList(Spreadsheet $spreadsheet): bool
    {
        $sheet = $spreadsheet->getActiveSheet();
        if ($sheet->getCell('A2')->getValue() !== "Прайс-лист") {
            return false;
        }
        if (mb_substr($sheet->getCell('A4')->getValue(), 0, mb_strlen("Телефон: +7 925 386-18-90 , Андрей")) !== "Телефон: +7 925 386-18-90 , Андрей") {
            return false;
        }
        if (mb_strpos($sheet->getCell('A4')->getValue(), "E-mail: allscent@list.ru") === false) {
            return false;
        }
        if ($sheet->getCell('A7')->getValue() !== "Валюта: USD") {
            return false;
        }
        if ($sheet->getCell('A9')->getValue() !== "Артикул") {
            return false;
        }
        if ($sheet->getCell('B9')->getValue() !== "Наименование") {
            return false;
        }
        if ($sheet->getCell('C9')->getValue() !== "Цена") {
            return false;
        }
        if ($sheet->getCell('D9')->getValue() !== "Заказ") {
            return false;
        }
        if ($sheet->getCell('E9')->getValue() !== "Сумма") {
            return false;
        }

        return true;
    }

    private function isBeautyParfumRuPriceList(Spreadsheet $spreadsheet): bool
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
        if ($spreadsheet->getSheetNames() == ["Лиcт2"]) {
            return false;
        }

        return true;
    }
}