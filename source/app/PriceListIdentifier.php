<?php

namespace App;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Enums\PriceListProviderEnum;

class PriceListIdentifier
{
    public function identiry(Spreadsheet $spreadsheet): PriceListProviderEnum
    {
        switch (true) {
            case $this->isDePerfumesUsdPriceList($spreadsheet):
                return PriceListProviderEnum::DePerfumesUsd;
            case $this->isNichePerfumeUsdPriceList($spreadsheet):
                return PriceListProviderEnum::NichePerfumeUsd;
            case $this->isAvangardUsdPriceList($spreadsheet):
                return PriceListProviderEnum::AvangardUsd;
            case $this->isBeliyUsdPriceList($spreadsheet):
                return PriceListProviderEnum::BeliyUsd;
            case $this->isGevorgUsdPriceList($spreadsheet):
                return PriceListProviderEnum::GevorgUsd;
            case $this->isGuldenUsdPriceList($spreadsheet):
                return PriceListProviderEnum::GuldenUsd;
            case $this->isZubarUsdPriceList($spreadsheet);
                return PriceListProviderEnum::ZubarUsd;
            case $this->isNashaFirmaUsdPriceList($spreadsheet);
                return PriceListProviderEnum::NashaFirmaUsd;
            case $this->isOrabelUsdPriceList($spreadsheet);
                return PriceListProviderEnum::OrabelUsd;
            case $this->isRagimovaDianaUsdPriceList($spreadsheet);
                return PriceListProviderEnum::RagimovaDianaUsd;
            case $this->isKurzinaUsdPriceList($spreadsheet):
                return PriceListProviderEnum::KurzinaUsd;
            default:
                return PriceListProviderEnum::Unknown;
        }
    }

    private function isDePerfumesUsdPriceList(Spreadsheet $spreadsheet): bool
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

    private function isAvangardUsdPriceList(Spreadsheet $spreadsheet): bool
    {
        $sheet = $spreadsheet->getActiveSheet();
        if ($sheet->getCell('C3')->getValue() !== "Прайс-лист") {
            return false;
        }
        if ($sheet->getCell('E3')->getValue() !== "Итого:") {
            return false;
        }
        if ($sheet->getCell('B6')->getValue() !== "Код") {
            return false;
        }
        if ($sheet->getCell('C6')->getValue() !== "Наименование товаров") {
            return false;
        }
        if ($sheet->getCell('D6')->getValue() !== "Цена") {
            return false;
        }
        if ($sheet->getCell('E6')->getValue() !== "Заказ") {
            return false;
        }
        if ($sheet->getCell('F6')->getValue() !== "Сумма") {
            return false;
        }

        return true;
    }

    private function isBeliyUsdPriceList(Spreadsheet $spreadsheet): bool
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

    private function isGevorgUsdPriceList(Spreadsheet $spreadsheet): bool
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

    private function isGuldenUsdPriceList(Spreadsheet $spreadsheet): bool
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

    private function isZubarUsdPriceList(Spreadsheet $spreadsheet): bool
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

    private function isNashaFirmaUsdPriceList(Spreadsheet $spreadsheet): bool
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

    private function isOrabelUsdPriceList(Spreadsheet $spreadsheet): bool
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

    private function isRagimovaDianaUsdPriceList(Spreadsheet $spreadsheet): bool
    {
        $sheet = $spreadsheet->getActiveSheet();
        if ($sheet->getCell('A4')->getValue() !== "Код") {
            return false;
        }
        if ($sheet->getCell('B4')->getValue() !== "Наименование товара") {
            return false;
        }
        if ($sheet->getCell('C4')->getValue() !== "Опт.цена") {
            return false;
        }
        if ($spreadsheet->getSheetNames() !== ["Лист1"]) {
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
}