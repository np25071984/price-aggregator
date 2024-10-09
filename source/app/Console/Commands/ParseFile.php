<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\ScanResult;
use App\Enums\SubStringPositionEnum;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Entities\Item;
use App\Entities\ItemSet;
use RuntimeException;

define("OUTPUT_FOLE_NAME", "output.xlsx",);
define("FORMAT_CURRENCY_RUB_INTEGER", '#,##0_-'); // '#,##0_-[$руб]'

class ParseFile extends Command
{
    private array $brands;
    private array $brandStopPhrases;
    private array $volumes;
    private array $types;
    private array $testerFlags;
    private array $brandLines;
    private array $brandSets;
    private array $oldDesignFlags;
    private array $artisanalBottlingFlags;
    private array $markingFlags;
    private array $sex;
    private array $damageFlags;
    private array $fillerWords;

    private array $files = [
        [
            "index_article" => 0,
            "index_title" => 1,
            "index_price" => 3,
            "first_row" => 2,
            "file_name" => "1.xlsx",
        ],
        [
            "index_article" => 0,
            "index_title" => 1,
            "index_price" => 2,
            "first_row" => 10,
            "file_name" => "AllScent.xlsx",
        ],
        [
            "index_article" => 1,
            "index_title" => 2,
            "index_price" => 3,
            "first_row" => 14,
            "file_name" => "ninche_perfume.xlsx",
        ],
        [
            "index_article" => 1,
            "index_title" => 2,
            "index_price" => 4,
            "first_row" => 5,
            "file_name" => "PRC_202410030154.xlsx",
        ],
    ];
    /**
     * + refill
     * + box/no box
     * + is a bag?
     * + is cream?
     */

    protected $signature = 'app:parse-file';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function __construct() {
        parent::__construct();

        // we want to sort all associative dictionaries by key length to avoid false hits
        $brands = include __DIR__ . "/../../../dictionaries/brands.php";
        uksort($brands, function($a, $b) {
            return mb_strlen($b) <=> mb_strlen($a);
        });
        $this->brands = $brands;

        $brandLines = include __DIR__ . "/../../../dictionaries/brandLines.php";
        $brandLinesSorted = [];
        foreach($brandLines as $brand => $items) {
            uksort($items, function($a, $b) {
                return mb_strlen($b) <=> mb_strlen($a);
            });
            $brandLinesSorted[$brand] = $items;
        }
        unset($brandLines);
        $this->brandLines = $brandLinesSorted;

        $brandSets = include __DIR__ . "/../../../dictionaries/brandSets.php";
        $brandSetsSorted = [];
        foreach($brandSets as $brand => $items) {
            uksort($items, function($a, $b) {
                return mb_strlen($b) <=> mb_strlen($a);
            });
            $brandSetsSorted[$brand] = $items;
        }
        unset($brandSets);
        $this->brandSets = $brandSetsSorted;

        $this->brandStopPhrases = include __DIR__ . "/../../../dictionaries/brandStopPhrases.php";
        $this->volumes = include __DIR__ . "/../../../dictionaries/volumes.php";
        $this->types = include __DIR__ . "/../../../dictionaries/types.php";
        $this->testerFlags = include __DIR__ . "/../../../dictionaries/testerFlags.php";
        $this->oldDesignFlags = include __DIR__ . "/../../../dictionaries/oldDesignFlags.php";
        $this->artisanalBottlingFlags = include __DIR__ . "/../../../dictionaries/artisanalBottlingFlags.php";
        $this->markingFlags = include __DIR__ . "/../../../dictionaries/markingFlags.php";
        $this->sex = include __DIR__ . "/../../../dictionaries/sex.php";
        $this->damageFlags = include __DIR__ . "/../../../dictionaries/damageFlags.php";
        $this->fillerWords = include __DIR__ . "/../../../dictionaries/fillerWords.php";
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $reader = IOFactory::createReader("Xlsx");
        $data = [];
        $b = [];
        foreach ($this->files as $fileData) {
            $spreadsheet = $reader->load(__DIR__ . "/../../../files/{$fileData["file_name"]}");
            $activeSheet = $spreadsheet->getActiveSheet();
            $highestRow = $activeSheet->getHighestRow();
            $rows = $activeSheet->rangeToArray("A{$fileData["first_row"]}:F{$highestRow}");
            // $rows = [[0, "12 parfumeurs francais  Fontainebleau  10мл отливант"]];
            $indexArticle = $fileData["index_article"];
            $indexTitle = $fileData["index_title"];
            $indexPrice = $fileData["index_price"];
            $item = null;
            foreach ($rows as $r) {
                if (empty($r[$indexArticle]) || empty($r[$indexTitle]) || $r[$indexArticle] === "НФ-00001873") {
                    continue;
                }
                echo "Original title: ", $r[$indexTitle], PHP_EOL;

                $normolizedItemName = $this->normolizeString($r[$indexTitle]);

                // remove filler words
                foreach ($this->fillerWords as $word) {
                    $fillerWordScanResult = $this->sacnStringForDictionaryValue($normolizedItemName, [$word]);
                    $itemBrand = $this->processScanResult($fillerWordScanResult, $normolizedItemName);
                }

                // determine brand
                $itemBrandScanResult = $this->sacnStringForDictionaryValue($normolizedItemName, $this->brands, $this->brandStopPhrases);
                $itemBrand = $this->processScanResult($itemBrandScanResult, $normolizedItemName, $this->brands);

                // determine set
                $itemSetLineScanResult = $this->sacnStringForDictionaryValue($normolizedItemName, $this->brandSets[$itemBrand] ?? []);
                $itemSetLine = $this->processScanResult($itemSetLineScanResult, $normolizedItemName, $this->brandSets[$itemBrand] ?? []);

                if (!is_null($itemSetLine)) {

                    echo "    brand: ", $itemBrand ?? "<unknown>", PHP_EOL;
                    echo "    set line: ", $itemSetLine ?? "<unknown>", PHP_EOL;
                    $item = new ItemSet(
                        originalValue: $r[$indexTitle],
                        provider: $fileData["file_name"],
                        brand: $itemBrand,
                        line: $itemSetLine,
                    );
                } else {
                    // determine brand line
                    $itemLine = null;
                    if (!is_null($itemBrand)) {
                        $itemLineScanResult = $this->sacnStringForDictionaryValue($normolizedItemName, $this->brandLines[$itemBrand] ?? []);
                        $itemLine = $this->processScanResult($itemLineScanResult, $normolizedItemName, $this->brandLines[$itemBrand] ?? []);
                    }

                    // determine volume
                    $itemVolumeScanResult = $this->sacnStringForDictionaryValue($normolizedItemName, $this->volumes);
                    $itemVolume = $this->processScanResult($itemVolumeScanResult, $normolizedItemName, $this->volumes);

                    // determine type
                    $itemTypeScanResult = $this->sacnStringForDictionaryValue($normolizedItemName, $this->types);
                    $itemType = $this->processScanResult($itemTypeScanResult, $normolizedItemName, $this->types);

                    // is artisan bottling
                    $itemArtisanalBottlinScanResult = $this->sacnStringForDictionaryValue($normolizedItemName, $this->artisanalBottlingFlags);
                    $itemIsArtisanalBottling = $this->processScanResult($itemArtisanalBottlinScanResult, $normolizedItemName);

                    // is merking
                    $itemMarkingScanResult = $this->sacnStringForDictionaryValue($normolizedItemName, $this->markingFlags);
                    $itemHasMarking = $this->processScanResult($itemMarkingScanResult, $normolizedItemName);

                    // is tester
                    $itemIsTesterScanResult = $this->sacnStringForDictionaryValue($normolizedItemName, $this->testerFlags);
                    $itemIsTester = $this->processScanResult($itemIsTesterScanResult, $normolizedItemName);

                    // is old design
                    $itemIsOldDesignScanResult = $this->sacnStringForDictionaryValue($normolizedItemName, $this->oldDesignFlags);
                    $itemIsOldDesign = $this->processScanResult($itemIsOldDesignScanResult, $normolizedItemName);

                    // sex
                    $itemSexScanResult = $this->sacnStringForDictionaryValue($normolizedItemName, $this->sex);
                    $itemSex = $this->processScanResult($itemSexScanResult, $normolizedItemName, $this->sex);

                    // is damaged
                    $itemIsDamagedScanResult = $this->sacnStringForDictionaryValue($normolizedItemName, $this->damageFlags);
                    $itemIsDamaged = $this->processScanResult($itemIsDamagedScanResult, $normolizedItemName);

                    echo "    brand: ", $itemBrand ?? "<unknown>", PHP_EOL;
                    echo "    line: ", $itemLine ?? "<unknown>", PHP_EOL;
                    echo "    volume: ", $itemVolume ?? "<unknown>", PHP_EOL;
                    echo "    type: ", $itemType ?? "<unknown>", PHP_EOL;
                    echo "    artisanal bottling: ", $itemIsArtisanalBottling ? "YES" : "NO", PHP_EOL;
                    echo "    has marking: ", $itemHasMarking ? "YES" : "NO", PHP_EOL;
                    echo "    is tester: ", $itemIsTester ? "YES" : "NO", PHP_EOL;
                    echo "    is old design: ", $itemIsOldDesign ? "YES" : "NO", PHP_EOL;
                    echo "    sex: ", $itemSex ?? "<unknown>", PHP_EOL;
                    echo "    is damaged: ", $itemIsDamaged ? "YES" : "NO", PHP_EOL;

                    $item = new Item(
                        originalValue: $r[$indexTitle],
                        provider: $fileData["file_name"],
                        brand: $itemBrand,
                        line: $itemLine,
                        volume: $itemVolume,
                        type: $itemType,
                        sex: $itemSex,
                        isArtisanalBottling: $itemIsArtisanalBottling,
                        hasMarking: $itemHasMarking,
                        isTester: $itemIsTester,
                        isOldDesign: $itemIsOldDesign,
                        isDamaged: $itemIsDamaged,
                    );

                    if (is_null($itemLine)) {
                        $finalItemName = trim($normolizedItemName);
                        if (mb_strlen($finalItemName) > 0) {
                            $b[$itemBrand][$finalItemName] = 1;
                        }
                    }
                }
                $title = $this->generateTitle($item);
                $data[$itemBrand][$title][] = $item;
            }
        }
        foreach ($b as $brand => $items) {
            if (true) { //!isset($this->brandLines[$brand])) {
                echo "\"", $brand, "\" => [", PHP_EOL;
                foreach (array_keys($items) as $item) {
                    echo "    \"", $item, "\" => \"" . ucwords($item) . "\",",PHP_EOL;
                }
                echo "],", PHP_EOL;
            }
        }

        $this->writeResult($data);
    }

    private function writeResult(array $data): void
    {
        if (file_exists(OUTPUT_FOLE_NAME)) {
            unlink(OUTPUT_FOLE_NAME);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(mb_substr("Прайс", 0, Worksheet::SHEET_TITLE_MAXIMUM_LENGTH, 'utf-8'));
        $sheet->getStyle("A:A")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("C:C")->getNumberFormat()->setFormatCode(FORMAT_CURRENCY_RUB_INTEGER);

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

        $sheet->getColumnDimension('F')->setWidth(14);
        $sheet->setCellValue("F1", "Кол-во аналогов");
        $sheet->getStyle("F:F")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("G1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("G:G")->getAlignment()->setWrapText(true);
        $sheet->setCellValue("G1", "Оригинальные значения");
        $sheet->getColumnDimension('G')->setWidth(120);

        $currentLine = 2;
        foreach ($data as $brand => $items) {
            $sheet->mergeCells("A{$currentLine}:D{$currentLine}");
            $sheet->getStyle("A{$currentLine}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("A{$currentLine}")->applyFromArray(['font' => [
                'bold' => true,
            ]]);
            $sheet->setCellValue("A{$currentLine}", $brand);
            $currentLine++;
            foreach ($items as $title => $providers) {
                $sheet->setCellValue("A{$currentLine}", "aaa");
                $sheet->setCellValue("B{$currentLine}", $title);
                $sheet->setCellValue("C{$currentLine}", 999.33);
                $sheet->setCellValue("F{$currentLine}", count($providers));
                $providerOriginalValuesString = "";
                foreach ($providers as $provider) {
                    $providerOriginalValuesString .= "{$provider->provider}: " . trim($provider->originalValue) . PHP_EOL;
                }
                $sheet->setCellValue("G{$currentLine}", trim($providerOriginalValuesString));
                $currentLine++;
            }
        }
        $writer = new Xlsx($spreadsheet);

        $writer->save(OUTPUT_FOLE_NAME);
    }

    private function generateTitle(Item|ItemSet $item): string
    {
        if ($item instanceof ItemSet) {
            return "{$item->brand} {$item->line} набор";
        }

        $title = $item->brand;
        if (!is_null($item->line)) {
            $title .= " {$item->line}";
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
            $title .= " разливант";
        }
        if ($item->hasMarking) {
            $title .= " маркировка";
        }
        if ($item->isTester) {
            $title .= " тестер";
        }
        if ($item->isOldDesign) {
            $title .= " старый дезайн";
        }
        if ($item->isDamaged) {
            $title .= " поврежден";
        }

        return $title;
    }

    private function processScanResult(?ScanResult $result, string &$itemTitle, ?array $dictionary = null): null|bool|string
    {
        if (is_null($result)) {
            return is_null($dictionary) ? false : null;
        }

        // update itemTitle
        switch ($result->positionInScannedString) {
            case SubStringPositionEnum::Match:
                $str = $result->dictionaryValue;
                break;
            case SubStringPositionEnum::Beginning:
                $str = $result->dictionaryValue . " ";
                break;
            case SubStringPositionEnum::End:
            case SubStringPositionEnum::Middle:
                $str = " " . $result->dictionaryValue;
                break;

        }
        $pattern = "/" . preg_quote($str, "/") . "/";
        $itemTitle = preg_replace($pattern, "", $itemTitle, 1);

        return is_null($dictionary) ? true : $dictionary[$result->dictionaryValue];
    }

    private function sacnStringForDictionaryValue(string $targetString, array $dictionary, array $stopPhrases = []): ?ScanResult
    {
        $targetStringSize = mb_strlen($targetString);
        if (array_is_list($dictionary)) {
            foreach ($dictionary as $dictionaryValue) {
                $dictionaryValueSize = mb_strlen($dictionaryValue);

                // if the stgings are equal
                if (($dictionaryValueSize === $targetStringSize) && ($targetString === $dictionaryValue)) {
                    return new ScanResult($dictionaryValue, SubStringPositionEnum::Match);
                }

                // they aren't equal and the search one is longer; early exit
                if ($dictionaryValueSize >= $targetStringSize) {
                    continue;
                }

                $position = $this->findSubStringPosition($targetString, $dictionaryValue);
                if (!is_null($position)) {
                    return new ScanResult($dictionaryValue, $position);
                }
            }
        } else {
            foreach ($dictionary as $dictionaryValue => $unifiedValue) {
                $dictionaryValueSize = mb_strlen($dictionaryValue);

                // if the stgings are equal
                if (($dictionaryValueSize === $targetStringSize) && ($targetString === $dictionaryValue)) {
                    return new ScanResult($dictionaryValue, SubStringPositionEnum::Match);
                }

                // they aren't equal and the search one is longer; early exit
                if ($dictionaryValueSize >= $targetStringSize) {
                    continue;
                }

                $position = $this->findSubStringPosition($targetString, $dictionaryValue);
                if (!is_null($position)) {
                    if ($this->isInStopList($targetString, $unifiedValue, $stopPhrases)) {
                        continue;
                    }

                    return new ScanResult($dictionaryValue, $position);
                }
            }
        }

        return null;
    }

    private function findSubStringPosition(string $haystack, string $needle): ?SubStringPositionEnum
    {
        $needleSize = mb_strlen($needle);

        /**
         * Spaces are very important because of business rules of creating price lists
         *
         * Firstly, let's check if the search string + space is located in the beginning of the haystack
         */
        $needleRightSpace = $needle . " ";
        if (mb_substr($haystack, 0, $needleSize + 1) === $needleRightSpace) {
            return SubStringPositionEnum::Beginning;
        }

        // Secondly, let's check if space + the search string is located in the end of the haystack
        $needleLeftSpace = " " . $needle;
        if (mb_substr($haystack, -1 * ($needleSize + 1)) === $needleLeftSpace) {
            return SubStringPositionEnum::End;
        }

        // neither in the beginning nor end; has to be surround by two spaces
        $needleWithSpaces = " {$needle} ";
        if (mb_strpos($haystack, $needleWithSpaces) !== false) {
            return SubStringPositionEnum::Middle;
        }

        return null;
    }

    private function isInStopList(string $name, string $unifiedValue, array $stopPhrases): bool
    {
        /**
         * Some brand names often can be found in line names; to address this problem we can put those line
         * names into appropriate brand stop phrase list to ignore such findings
         */
        if (isset($stopPhrases[$unifiedValue]) && !empty($stopPhrases[$unifiedValue])) {
            foreach ($stopPhrases[$unifiedValue] as $stopPhrase) {
                if (mb_strpos($name, $stopPhrase) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    private function normolizeString(string $string): string
    {
        $string = mb_strtolower($string);

        /**
         * little data hacks
         * TODO: possible multibyte issue; replace str_replace function
         */
        $string = str_replace(" ", " ", $string);
        $string = preg_replace('/\s{2,}/', " ", $string);

        $string = str_replace("ml отливант5", "5ml отливант", $string);
        $string = str_replace(" mltest", " ml test", $string);
        $string = str_replace("ПАКЕТ AJMAL CRAFTING MEMORIES", "AJMAL CRAFTING MEMORIES ПАКЕТ", $string);
        $string = str_replace("ПАКЕТ AJMAL SIGNATURE", "AJMAL SIGNATURE ПАКЕТ", $string);
        $string = str_replace("ПАКЕТ PHILLY PHILL", "PHILLY PHILL ПАКЕТ", $string);

        return $string;
    }
}