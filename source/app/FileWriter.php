<?php

namespace App;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use App\Entities\AbstractProductEntity;
use App\Entities\BagEntity;
use App\Entities\PerfumeEntity;
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
        $sheet->setCellValue("H1", "Тип");
        $sheet->setCellValue("I1", "Объем");
        $sheet->setCellValue("J1", "Тестер");
        $sheet->setCellValue("K1", "Сэмпл");
        $sheet->setCellValue("L1", "Старый дизайн");
        $sheet->setCellValue("M1", "Разливант");
        $sheet->setCellValue("N1", "Маркировка");
        $sheet->setCellValue("O1", "Рефилл");
        $sheet->setCellValue("P1", "Повреждение");
        $sheet->setCellValue("Q1", "Пол");

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
                case $item instanceof PerfumeEntity:
                    $sheet->setCellValue("G{$currentLine}", $item->brand);
                    $sheet->setCellValue("H{$currentLine}", $item->type);
                    $sheet->setCellValue("I{$currentLine}", $item->volume);
                    $sheet->setCellValue("J{$currentLine}", $item->isTester ? "tester" : "");
                    $sheet->setCellValue("K{$currentLine}", $item->isSample ? "sample" : "");
                    $sheet->setCellValue("L{$currentLine}", $item->isOldDesign ? "old design" : "");
                    $sheet->setCellValue("M{$currentLine}", $item->isArtisanalBottling ? "разливант" : "");
                    $sheet->setCellValue("N{$currentLine}", $item->hasMarking ? "маркировка" : "");
                    $sheet->setCellValue("O{$currentLine}", $item->isRefill ? "refill" : "");
                    $sheet->setCellValue("P{$currentLine}", $item->isDamaged ? "поврежден" : "");
                    $sheet->setCellValue("Q{$currentLine}", $item->sex);
                    break;
            }

            $currentLine++;
        }
        $writer = new Xlsx($spreadsheet);
        $writer->save($fileName);
    }
}