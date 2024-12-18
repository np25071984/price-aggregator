<?php

namespace App;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

readonly class FileMergeWriter
{
    private const FORMAT_CURRENCY_RUB_INTEGER = '#,##0_-'; // '#,##0_-[$руб]'

    public function save(string $fileName, array $data): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(mb_substr("Прайс", 0, Worksheet::SHEET_TITLE_MAXIMUM_LENGTH, 'utf-8'));
        $sheet->getStyle("A:A")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("C:C")->getNumberFormat()->setFormatCode(self::FORMAT_CURRENCY_RUB_INTEGER);

        $sheet->getColumnDimension('A')->setWidth(16.5);
        $sheet->getColumnDimension('B')->setWidth(89);
        $sheet->getColumnDimension('C')->setWidth(22.5);
        $sheet->getColumnDimension('D')->setWidth(22.5);
        $sheet->getStyle("A1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue("A1", "Артикул");
        $sheet->getStyle("B1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue("B1", "Наименование");
        $sheet->getStyle("C1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue("C1", "Цена");
        $sheet->getStyle("D1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue("D1", "Заказ");

        $currentLine = 2;
        foreach ($data as $brand => $items) {
            $sheet->mergeCells("A{$currentLine}:D{$currentLine}");
            $sheet->getStyle("A{$currentLine}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("A{$currentLine}")->applyFromArray(['font' => [
                'bold' => true,
            ]]);
            $sheet->setCellValue("A{$currentLine}", $brand);
            $currentLine++;
            foreach ($items as $item) {
                $sheet->setCellValue("A{$currentLine}", $item[0]);
                $sheet->setCellValue("B{$currentLine}", $item[1]);
                $sheet->setCellValue("C{$currentLine}", $item[2]);
                $currentLine++;
            }
        }
        $writer = new Xlsx($spreadsheet);

        $writer->save($fileName);
    }
}