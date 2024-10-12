<?php

namespace App;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use App\Entities\AbstractPriceListItemEntity;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

readonly class FileWriter
{
    /**
     * @param AbstractPriceListItemEntity[] $data
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

        $currentLine = 2;
        foreach ($data as $item) {
            $sheet->setCellValue("A{$currentLine}", $item->article);
            $sheet->setCellValue("B{$currentLine}", $item->originalTitle);
            $sheet->setCellValue("C{$currentLine}", $item->price);
            $sheet->setCellValue("F{$currentLine}", $item->provider->value);
            $sheet->setCellValue("G{$currentLine}", $item->brand);
            $currentLine++;
        }
        $writer = new Xlsx($spreadsheet);
        $writer->save($fileName);
    }
}