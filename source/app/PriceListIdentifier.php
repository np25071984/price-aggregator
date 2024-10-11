<?php

namespace App;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Enums\PriceListProviderEnum;

class PriceListIdentifier
{
    public function identiry(Spreadsheet $spreadsheet): PriceListProviderEnum
    {
        switch (true) {
            case $this->isPrice1310UsdPriceList($spreadsheet):
                return PriceListProviderEnum::Price1310Usd;
            case $this->isNichePerfumeUsdPriceList($spreadsheet):
                return PriceListProviderEnum::NichePerfumeUsd;
            case $this->isKurzinaUsdPriceList($spreadsheet):
                return PriceListProviderEnum::KurzinaUsd;
            case $this->isAllScentUsdPriceList($spreadsheet):
                return PriceListProviderEnum::AllScentUsd;
            case $this->isBeautyParfumUsdPriceList($spreadsheet):
                return PriceListProviderEnum::BeautyPerfumeUsd;
            case $this->isPricePRCUsdPriceList($spreadsheet);
                return PriceListProviderEnum::PricePRCUsd;
            case $this->isPriceParfumUsdPriceList($spreadsheet);
                return PriceListProviderEnum::PriceParfumUsd;
            case $this->isPafumStockUsdPriceList($spreadsheet);
                return PriceListProviderEnum::PafumStockUsd;
            default:
                return PriceListProviderEnum::Unknown;
        }
    }

    private function isPrice1310UsdPriceList(Spreadsheet $spreadsheet): bool
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

    private function isKurzinaUsdPriceList(Spreadsheet $spreadsheet): bool
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
        if ($spreadsheet->getSheetNames() !== ["Лист2"]) {
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

    private function isBeautyParfumUsdPriceList(Spreadsheet $spreadsheet): bool
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
        if ($spreadsheet->getSheetNames() !== ["Лист1"]) {
            return false;
        }

        return true;
    }

    private function isPricePRCUsdPriceList(Spreadsheet $spreadsheet): bool
    {
        $sheet = $spreadsheet->getActiveSheet();
        if ($sheet->getCell('B2')->getValue() !== "Прайс-лист") {
            return false;
        }
        if ($sheet->getCell('B3')->getValue() !== "Код") {
            return false;
        }
        if ($sheet->getCell('C3')->getValue() !== "Наименование") {
            return false;
        }
        if ($sheet->getCell('D3')->getValue() !== "Оптовая цена") {
            return false;
        }
        if ($spreadsheet->getSheetNames() !== ["Лист_1"]) {
            return false;
        }

        return true;
    }

    private function isPriceParfumUsdPriceList(Spreadsheet $spreadsheet): bool
    {
        $sheet = $spreadsheet->getActiveSheet();
        if ($sheet->getCell('A1')->getValue() !== "Прайс Раритет. +7(985)363-99-11") {
            return false;
        }
        if ($sheet->getCell('A2')->getValue() !== "Код") {
            return false;
        }
        if ($sheet->getCell('B2')->getValue() !== "Наименование") {
            return false;
        }
        if ($sheet->getCell('D2')->getValue() !== "Цена") {
            return false;
        }
        if ($sheet->getCell('E2')->getValue() !== "Заказ") {
            return false;
        }
        if ($spreadsheet->getSheetNames() !== ["Лист1", "Лист2", "Лист3"]) {
            return false;
        }

        return true;
    }

    private function isPafumStockUsdPriceList(Spreadsheet $spreadsheet): bool
    {
        $sheet = $spreadsheet->getActiveSheet();
        if ($sheet->getCell('A1')->getValue() !== "ПАРФЮМ СТОК ") {
            return false;
        }
        if ($sheet->getCell('B2')->getValue() !== "НАИМЕНОВАНИЕ") {
            return false;
        }
        if ($sheet->getCell('C2')->getValue() !== "Прайс-лист") {
            return false;
        }
        if ($sheet->getCell('C3')->getValue() !== "Цена") {
            return false;
        }
        if ($sheet->getCell('D3')->getValue() !== "Заказ") {
            return false;
        }
        if ($spreadsheet->getSheetNames() !== ["TDSheet"]) {
            return false;
        }

        return true;
    }
}