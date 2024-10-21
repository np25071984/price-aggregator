<?php

namespace App;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use App\Entities\Products\AbstractProductEntity;
use App\Entities\Products\BagEntity;
use App\Entities\Products\CandleEntity;
use App\Entities\Products\PerfumeEntity;
use App\Entities\Products\ShampooAndGelEntity;
use App\Entities\Products\BodyLotionEntity;
use App\Entities\Products\BodyOilEntity;
use App\Entities\Products\CableEntity;
use App\Entities\Products\HandCreamEntity;
use App\Entities\Products\BodyCreamEntity;
use App\Entities\Products\BathCreamEntity;
use App\Entities\Products\SoapEntity;
use App\Entities\Products\AtomiserEntity;
use App\Entities\Products\LaundryDetergentEntity;
use App\Entities\Products\DeoStickEntity;
use App\Entities\Products\OtherProductEntity;
use App\Entities\Products\SetEntity;
use App\Entities\Products\ShowerGelEntity;
use App\Entities\Products\UnknownProductEntity;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

readonly class FileWriter
{
    /**
     * @param AbstractProductEntity[] $data
     */
    public function save(string $fileName, array $data): void
    {
        if (file_exists($fileName)) {
            unlink($fileName);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(mb_substr("Прайс", 0, Worksheet::SHEET_TITLE_MAXIMUM_LENGTH, 'utf-8'));
        $sheet->getStyle("A:A")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("C:C")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_CURRENCY_USD);

        $sheet->getColumnDimension('A')->setWidth(16.5);
        $sheet->getColumnDimension('B')->setWidth(89);
        $sheet->getColumnDimension('C')->setWidth(22.5);
        $sheet->getColumnDimension('D')->setWidth(22.5);
        $sheet->getColumnDimension('F')->setWidth(22.5);
        $sheet->getStyle("A1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue("A1", "Артикул");
        $sheet->getStyle("B1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue("B1", "Наименование");
        $sheet->getStyle("C1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue("C1", "Цена");
        $sheet->getStyle("D1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue("D1", "Заказ");


        $sheet->setCellValue("F1", "Поставщик");
        $sheet->setCellValue("G1", "Бренд");
        $sheet->setCellValue("H1", "Наименование");
        $sheet->setCellValue("I1", "Тип");
        $sheet->setCellValue("J1", "Объем");
        $sheet->setCellValue("K1", "Тестер");
        $sheet->setCellValue("L1", "Сэмпл");
        $sheet->setCellValue("M1", "Старый дизайн");
        $sheet->setCellValue("N1", "Разливант");
        $sheet->setCellValue("O1", "Маркировка");
        $sheet->setCellValue("P1", "Рефилл");
        $sheet->setCellValue("Q1", "Повреждение");
        $sheet->setCellValue("R1", "Пол");

        $currentLine = 2;
        foreach ($data as $item) {
            $sheet->setCellValue("A{$currentLine}", $item->article);
            $sheet->setCellValue("B{$currentLine}", $item->originalTitle);
            $sheet->setCellValue("C{$currentLine}", $item->price);
            $sheet->setCellValue("F{$currentLine}", $item->provider->value);

            switch (true) {
                case $item instanceof BagEntity:
                    $sheet->mergeCells("G{$currentLine}:Q{$currentLine}");
                    $sheet->setCellValue("G{$currentLine}", "упаковка");
                    break;
                case $item instanceof CandleEntity:
                    $sheet->mergeCells("G{$currentLine}:Q{$currentLine}");
                    $sheet->setCellValue("G{$currentLine}", "свеча");
                    break;
                case $item instanceof ShampooAndGelEntity:
                    $sheet->mergeCells("G{$currentLine}:Q{$currentLine}");
                    $sheet->setCellValue("G{$currentLine}", "Шампунь и гель");
                    break;
                case $item instanceof BodyLotionEntity:
                    $sheet->mergeCells("G{$currentLine}:Q{$currentLine}");
                    $sheet->setCellValue("G{$currentLine}", "Лосьон для тела");
                    break;
                case $item instanceof BodyOilEntity:
                    $sheet->mergeCells("G{$currentLine}:Q{$currentLine}");
                    $sheet->setCellValue("G{$currentLine}", "Масло для тела");
                    break;
                case $item instanceof CableEntity:
                    $sheet->mergeCells("G{$currentLine}:Q{$currentLine}");
                    $sheet->setCellValue("G{$currentLine}", "Шнур");
                    break;
                case $item instanceof HandCreamEntity:
                    $sheet->mergeCells("G{$currentLine}:Q{$currentLine}");
                    $sheet->setCellValue("G{$currentLine}", "Крем для рук");
                    break;
                case $item instanceof BodyCreamEntity:
                    $sheet->mergeCells("G{$currentLine}:Q{$currentLine}");
                    $sheet->setCellValue("G{$currentLine}", "Крем для тела");
                    break;
                case $item instanceof BathCreamEntity:
                    $sheet->mergeCells("G{$currentLine}:Q{$currentLine}");
                    $sheet->setCellValue("G{$currentLine}", "Крем для ванны");
                    break;
                case $item instanceof AtomiserEntity:
                    $sheet->mergeCells("G{$currentLine}:Q{$currentLine}");
                    $sheet->setCellValue("G{$currentLine}", "Атомайзер");
                    break;
                case $item instanceof SoapEntity:
                    $sheet->mergeCells("G{$currentLine}:Q{$currentLine}");
                    $sheet->setCellValue("G{$currentLine}", "Мыло");
                    break;
                case $item instanceof LaundryDetergentEntity:
                    $sheet->mergeCells("G{$currentLine}:Q{$currentLine}");
                    $sheet->setCellValue("G{$currentLine}", "Жидкий порошок");
                    break;
                case $item instanceof DeoStickEntity:
                    $sheet->mergeCells("G{$currentLine}:Q{$currentLine}");
                    $sheet->setCellValue("G{$currentLine}", "Деодорант");
                    break;
                case $item instanceof ShowerGelEntity:
                    $sheet->mergeCells("G{$currentLine}:Q{$currentLine}");
                    $sheet->setCellValue("G{$currentLine}", "Гель для душа");
                    break;
                case $item instanceof OtherProductEntity:
                    $sheet->mergeCells("G{$currentLine}:Q{$currentLine}");
                    $sheet->setCellValue("G{$currentLine}", "Разное");
                    break;
                case $item instanceof SetEntity:
                    $sheet->mergeCells("G{$currentLine}:Q{$currentLine}");
                    $sheet->setCellValue("G{$currentLine}", "Набор");
                    break;
                case $item instanceof UnknownProductEntity:
                    $sheet->mergeCells("G{$currentLine}:Q{$currentLine}");
                    $sheet->setCellValue("G{$currentLine}", "Нераспознанный продукт");
                    break;
                case $item instanceof PerfumeEntity:
                    $sheet->setCellValue("G{$currentLine}", $item->brand);
                    $sheet->setCellValue("H{$currentLine}", $item->name);
                    $sheet->setCellValue("I{$currentLine}", $item->type);
                    $sheet->setCellValue("J{$currentLine}", $item->volume);
                    $sheet->setCellValue("K{$currentLine}", $item->isTester ? "tester" : "");
                    $sheet->setCellValue("L{$currentLine}", $item->isSample ? "sample" : "");
                    $sheet->setCellValue("M{$currentLine}", $item->isOldDesign ? "old design" : "");
                    $sheet->setCellValue("N{$currentLine}", $item->isArtisanalBottling ? "разливант" : "");
                    $sheet->setCellValue("O{$currentLine}", $item->hasMarking ? "маркировка" : "");
                    $sheet->setCellValue("P{$currentLine}", $item->isRefill ? "refill" : "");
                    $sheet->setCellValue("Q{$currentLine}", $item->isDamaged ? "поврежден" : "");
                    $sheet->setCellValue("R{$currentLine}", $item->sex);
                    break;
            }

            $currentLine++;
        }
        $writer = new Xlsx($spreadsheet);
        $writer->save($fileName);
    }
}