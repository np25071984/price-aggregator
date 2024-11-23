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
        $sheet->getStyle("A1")->getFont()->setBold(true);
        $sheet->setCellValue("A1", "Артикул");
        $sheet->getStyle("B1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("B1")->getFont()->setBold(true);
        $sheet->setCellValue("B1", "Наименование");
        $sheet->getStyle("C1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("C1")->getFont()->setBold(true);
        $sheet->setCellValue("C1", "Цена");
        $sheet->getStyle("D1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("D1")->getFont()->setBold(true);
        $sheet->setCellValue("D1", "Заказ");

        $perfumesByBrand = [];
        $setsByBrand = [];
        foreach ($data as $item) {
            switch (true) {
                case $item instanceof PerfumeEntity:
                    if (is_null($item->name)) {
                        continue;
                    }

                    $perfumeTtitle = $this->generatePerfumeTitle($item);
                    $perfumesByBrand[$item->brand][$perfumeTtitle][] = $item;
                    break;
                case $item instanceof SetEntity:
                    $setTitle = $this->generateSetTitle($item);
                    $setsByBrand[$item->brand][$setTitle][] = $item;
                    break;
            }
        }

        $currentLine = 2;
        foreach ($perfumesByBrand as $brand => $titles) {
            $sheet->mergeCells("A{$currentLine}:D{$currentLine}");
            $sheet->getStyle("A{$currentLine}:D{$currentLine}")->getFont()->setBold(true);
            $sheet->setCellValue("A{$currentLine}", $brand);
            $currentLine++;
            foreach ($titles as $title => $items) {
                $currentGroupHeadLine = $currentLine;
                $currentGroupHeadArticle = "XXXXX";
                $currentGroupHeadPrice = PHP_FLOAT_MAX;
                $sheet->setCellValue("B{$currentLine}", $title . " [" . count($items) . "]");
                $currentLine++;

                foreach ($items as $item) {
                    if ($item->price < $currentGroupHeadPrice) {
                        $currentGroupHeadPrice = $item->price;
                        $currentGroupHeadArticle = $item->article;
                    }
                    $sheet->setCellValue("A{$currentLine}", $item->article);
                    $sheet->setCellValue("B{$currentLine}", "({$item->provider->value}) " . $item->originalTitle);
                    $sheet->setCellValue("C{$currentLine}", $item->price);
                    $sheet->getRowDimension($currentLine)
                        ->setOutlineLevel(1)
                        ->setVisible(false)
                        ->setCollapsed(true);
                    $currentLine++;
                }
                $sheet->setCellValue("A{$currentGroupHeadLine}", $currentGroupHeadArticle);
                $sheet->setCellValue("C{$currentGroupHeadLine}", $currentGroupHeadPrice);
            }

            foreach ($setsByBrand[$brand] ?? [] as $setTitle => $items) {
                $currentGroupHeadLine = $currentLine;
                $currentGroupHeadArticle = "XXXXX";
                $currentGroupHeadPrice = PHP_FLOAT_MAX;

                $sheet->setCellValue("B{$currentLine}", $setTitle . " [" . count($items) . "]");
                $currentLine++;

                foreach ($items as $item) {
                    if ($item->price < $currentGroupHeadPrice) {
                        $currentGroupHeadPrice = $item->price;
                        $currentGroupHeadArticle = $item->article;
                    }
                    $sheet->setCellValue("A{$currentLine}", $item->article);
                    $sheet->setCellValue("B{$currentLine}", "({$item->provider->value}) " . $item->originalTitle);
                    $sheet->setCellValue("C{$currentLine}", $item->price);
                    $sheet->getRowDimension($currentLine)
                        ->setOutlineLevel(1)
                        ->setVisible(false)
                        ->setCollapsed(true);
                    $currentLine++;
                }

                $sheet->setCellValue("A{$currentGroupHeadLine}", $currentGroupHeadArticle);
                $sheet->setCellValue("C{$currentGroupHeadLine}", $currentGroupHeadPrice);
            }
        }
        unset($perfumesByBrand);
        unset($setsByBrand);

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
        $sheet->getColumnDimension('G')->setWidth(30);
        $sheet->getColumnDimension('H')->setWidth(50);
        $sheet->getStyle("A1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue("A1", "Артикул");
        $sheet->getStyle("B1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue("B1", "Наименование");
        $sheet->getStyle("C1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue("C1", "Цена");

        $sheet->setCellValue("F1", "Поставщик");
        $sheet->setCellValue("G1", "Причина");
        $sheet->setCellValue("H1", "Комментарий");

        $currentLine = 2;
        foreach ($data as $item) {
            switch (true) {
                case $item instanceof BagEntity:
                    $sheet->setCellValue("G{$currentLine}", "Упаковка");
                    break;
                case $item instanceof CandleEntity:
                    $sheet->setCellValue("G{$currentLine}", "Свеча");
                    break;
                case $item instanceof ShampooAndGelEntity:
                    $sheet->setCellValue("G{$currentLine}", "Шампунь и гель");
                    break;
                case $item instanceof BodyLotionEntity:
                    $sheet->setCellValue("G{$currentLine}", "Лосьон для тела");
                    break;
                case $item instanceof BodyOilEntity:
                    $sheet->setCellValue("G{$currentLine}", "Масло для тела");
                    break;
                case $item instanceof CableEntity:
                    $sheet->setCellValue("G{$currentLine}", "Шнур");
                    break;
                case $item instanceof HandCreamEntity:
                    $sheet->setCellValue("G{$currentLine}", "Крем для рук");
                    break;
                case $item instanceof BodyCreamEntity:
                    $sheet->setCellValue("G{$currentLine}", "Крем для тела");
                    break;
                case $item instanceof BathCreamEntity:
                    $sheet->setCellValue("G{$currentLine}", "Крем для ванны");
                    break;
                case $item instanceof AtomiserEntity:
                    $sheet->setCellValue("G{$currentLine}", "Атомайзер");
                    break;
                case $item instanceof SoapEntity:
                    $sheet->setCellValue("G{$currentLine}", "Мыло");
                    break;
                case $item instanceof LaundryDetergentEntity:
                    $sheet->setCellValue("G{$currentLine}", "Жидкий порошок");
                    break;
                case $item instanceof DeoStickEntity:
                    $sheet->setCellValue("G{$currentLine}", "Деодорант");
                    break;
                case $item instanceof ShowerGelEntity:
                    $sheet->setCellValue("G{$currentLine}", "Гель для душа");
                    break;
                case $item instanceof OtherProductEntity:
                    $sheet->setCellValue("G{$currentLine}", "Разное");
                    break;
                case $item instanceof SetEntity:
                    continue 2;
                    break;
                case $item instanceof UnknownProductEntity:
                    $sheet->setCellValue("G{$currentLine}", "Нераспознанный продукт");
                    break;
                case $item instanceof PerfumeEntity:
                    if (!is_null($item->name)) {
                        continue 2;
                    }
                    $sheet->setCellValue("G{$currentLine}", "Нераспознанное название");
                    $titleCaseName = mb_convert_case($item->comment, MB_CASE_TITLE);
                    $b = $item->brand ?? "<unknown_brand";
                    $sheet->setCellValue("H{$currentLine}", "{$b}: \"{$item->comment}\" => \"{$titleCaseName}\",");
                    break;
                default:
                    throw new RuntimeException("Unknown object!");
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

    private function generatePerfumeTitle(PerfumeEntity $item): string
    {
        $title = $item->brand;

        /**
         * sometimes name can be an empty string; for example when we
         * have the same brand and name we don't want to douple them
         */
        if (!empty($item->name)) {
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
        if ($item->isLimited) {
            $title .= " Limited edition";
        }
        // if (!is_null($item->hasCap)) {
        //     $title .= $item->hasCap ? " с крышкой" : " без крышки";
        // }
        if ($item->isArtisanalBottling) {
            $title .= " отливант";
        }
        // if ($item->hasMarking) {
        //     $title .= " маркировка";
        // }
        if ($item->isTester || ($item->hasCap === false)) {
            $title .= " tester";
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

    private function generateSetTitle(SetEntity $item): string
    {
        return $item->brand . " " . $item->line;
    }
}