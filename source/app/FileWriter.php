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
use RuntimeException;

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

        $perfumesByBrand = [];
        foreach ($data as $item) {
            if (!($item instanceof PerfumeEntity)) {
                continue;
            }

            if (is_null($item->name)) {
                continue;
            }

            $title = $this->generateTitle($item);
            $perfumesByBrand[$item->brand][$title][] = $item;

        }

        $currentLine = 2;
        foreach ($perfumesByBrand as $brand => $titles) {
            $sheet->mergeCells("A{$currentLine}:D{$currentLine}");
            $sheet->getStyle("A{$currentLine}:D{$currentLine}")->getFont()->setBold(true);
            $sheet->setCellValue("A{$currentLine}", $brand);
            $currentLine++;
            foreach ($titles as $title => $items) {
                $sheet->setCellValue("A{$currentLine}", "XXXXX");
                $sheet->setCellValue("B{$currentLine}", $title . " [" . count($items) . "]");
                $sheet->setCellValue("C{$currentLine}", 0.00);
                $currentLine++;

                foreach ($items as $item) {
                    $sheet->setCellValue("A{$currentLine}", $item->article);
                    $sheet->setCellValue("B{$currentLine}", "({$item->provider->value}) " . $item->originalTitle);
                    $sheet->setCellValue("C{$currentLine}", $item->price);
                    $sheet->getRowDimension($currentLine)
                        ->setOutlineLevel(1)
                        ->setVisible(false)
                        ->setCollapsed(true);
                    $currentLine++;
                }
            }
        }
        unset($perfumesByBrand);

        $spreadsheet->createSheet(1);
        $spreadsheet->setActiveSheetIndex(1);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(mb_substr("Не распознанное", 0, Worksheet::SHEET_TITLE_MAXIMUM_LENGTH, 'utf-8'));
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

        $sheet->setCellValue("F1", "Поставщик");
        $sheet->setCellValue("G1", "Бренд");
        $sheet->setCellValue("H1", "Наименование");
        $sheet->setCellValue("I1", "fix");
        $sheet->setCellValue("J1", "Тип");
        $sheet->setCellValue("K1", "Объем");
        $sheet->setCellValue("L1", "Тестер");
        $sheet->setCellValue("M1", "Сэмпл");
        $sheet->setCellValue("N1", "Старый дизайн");
        $sheet->setCellValue("O1", "Разливант");
        $sheet->setCellValue("P1", "Маркировка");
        $sheet->setCellValue("Q1", "Рефилл");
        $sheet->setCellValue("R1", "Повреждение");
        $sheet->setCellValue("S1", "Пол");

        $currentLine = 2;
        foreach ($data as $item) {
            switch (true) {
                case $item instanceof BagEntity:
                    $sheet->mergeCells("G{$currentLine}:Q{$currentLine}");
                    $sheet->setCellValue("G{$currentLine}", "Упаковка");
                    break;
                case $item instanceof CandleEntity:
                    $sheet->mergeCells("G{$currentLine}:Q{$currentLine}");
                    $sheet->setCellValue("G{$currentLine}", "Свеча");
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
                    if (!is_null($item->name)) {
                        continue 2;
                    }
                    $sheet->setCellValue("G{$currentLine}", $item->brand);
                    $sheet->mergeCells("H{$currentLine}:Q{$currentLine}");
                    $sheet->setCellValue("H{$currentLine}", "<unknown_name>");
                    break;
                default:
                    throw new RuntimeException("Unknowsn item");
            }
            $sheet->setCellValue("A{$currentLine}", $item->article);
            $sheet->setCellValue("B{$currentLine}", $item->originalTitle);
            $sheet->setCellValue("C{$currentLine}", $item->price);
            $sheet->setCellValue("F{$currentLine}", $item->provider->value);

            $currentLine++;
        }
        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        $writer->save($fileName);
    }

    private function generateTitle(PerfumeEntity $item): string
    {
        $title = $item->brand;
        if (!is_null($item->name)) {
            $title .= " {$item->name}";
        }
        if (!is_null($item->volume)) {
            $title .= " {$item->volume}ml";
        }
        if (!is_null($item->type)) {
            $title .= " {$item->type}";
        }
        if (!is_null($item->sex)) {
            $title .= " {$item->sex}";
        }
        if ($item->isArtisanalBottling) {
            $title .= " отливант";
        }
        if ($item->hasMarking) {
            $title .= " маркировка";
        }
        if ($item->isTester) {
            $title .= " тестер";
        }
        if ($item->isSample) {
            $title .= " sample";
        }
        if ($item->isOldDesign) {
            $title .= " старый дезайн";
        }
        if ($item->isRefill) {
            $title .= " refill";
        }
        if ($item->isDamaged) {
            $title .= " поврежден";
        }

        return $title;
    }
}