<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Entities\ScanResultEntity;
use App\Enums\SubStringPositionEnum;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Entities\PriceListItemEntity;
use App\Entities\PriceListItemSetEntity;
use App\PriceListReader;
use App\DirectoryReader;
use Psy\Readline\Hoa\FileRead;
use App\PriceListIdentifier;
use App\Converters\PriceListConverterFactory;
use App\Enums\PriceListProviderEnum;
use App\FileWriter;

class ParseFile extends Command
{
    private array $brands;
    private array $brandStopPhrases;
    private array $volumes;
    private array $types;
    private array $testerFlags;
    private array $sampleFlags;
    private array $brandLines;
    private array $brandSets;
    private array $oldDesignFlags;
    private array $artisanalBottlingFlags;
    private array $markingFlags;
    private array $sex;
    private array $damageFlags;
    private array $refillFlags;
    private array $fillerWords;

    /**
     * + box/no box
     * + is a bag?
     * + is cream?
     * + is lotion?
     * + candle
     * + laundry
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
        $this->sampleFlags = include __DIR__ . "/../../../dictionaries/sampleFlags.php";
        $this->oldDesignFlags = include __DIR__ . "/../../../dictionaries/oldDesignFlags.php";
        $this->artisanalBottlingFlags = include __DIR__ . "/../../../dictionaries/artisanalBottlingFlags.php";
        $this->markingFlags = include __DIR__ . "/../../../dictionaries/markingFlags.php";
        $this->sex = include __DIR__ . "/../../../dictionaries/sex.php";
        $this->damageFlags = include __DIR__ . "/../../../dictionaries/damageFlags.php";
        $this->refillFlags = include __DIR__ . "/../../../dictionaries/refillFlags.php";
        $this->fillerWords = include __DIR__ . "/../../../dictionaries/fillerWords.php";
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        ini_set('memory_limit', '512M');

        $directoryReader = new DirectoryReader(__DIR__ . "/../../../files/");
        $priceListIdentifier = new PriceListIdentifier();
        $priceListConverterFactory = new PriceListConverterFactory();
        $data = [];
        foreach ($directoryReader->read(["xlsx", "xls"]) as $filePathName => $extension) {
            echo $filePathName, PHP_EOL;
            $reader = IOFactory::createReader($extension);
            $spreadsheet = $reader->load($filePathName);

            // identify price list
            $priceId = $priceListIdentifier->identiry($spreadsheet);
            echo "    ", $priceId->value, PHP_EOL;
            $converter = $priceListConverterFactory->getConverter($priceId);
            echo "    ", $converter::class, PHP_EOL;
            $data = array_merge($data, $converter->convert($spreadsheet, basename($filePathName)));
            unset($reader);
            unset($spreadsheet);
        }

        $writer = new FileWriter();
        $writer->save("output.xlsx", $data);

//         $reader = IOFactory::createReader("Xlsx");
//         $data = [];
//         $b = [];
//         foreach ($this->files as $fileData) {
//             $spreadsheet = $reader->load(__DIR__ . "/../../../files/{$fileData["file_name"]}");
//             $activeSheet = $spreadsheet->getActiveSheet();
//             $highestRow = $activeSheet->getHighestRow();
//             $rows = $activeSheet->rangeToArray("A{$fileData["first_row"]}:F{$highestRow}");
//             $rows = [[1, "ср Ф-ка Алые Паруса г.Николаев Свидание духи 1986г."]];
//             $indexArticle = $fileData["index_article"];
//             $indexTitle = $fileData["index_title"];
//             $indexPrice = $fileData["index_price"];
//             $item = null;
//             foreach ($rows as $r) {
//                 if (empty($r[$indexArticle]) || empty($r[$indexTitle]) || $r[$indexArticle] === "НФ-00001873") {
//                     continue;
//                 }
//                 echo "Original title: ", $r[$indexTitle], PHP_EOL;

//                 $normolizedItemName = $this->normolizeString($r[$indexTitle]);

//                 // remove filler words
//                 foreach ($this->fillerWords as $word) {
//                     $fillerWordScanResult = $this->sacnStringForDictionaryValue($normolizedItemName, [$word]);
//                     $itemBrand = $this->processScanResult($fillerWordScanResult, $normolizedItemName);
//                 }

//                 // determine brand
//                 $itemBrandScanResult = $this->sacnStringForDictionaryValue($normolizedItemName, $this->brands, $this->brandStopPhrases);
//                 $itemBrand = $this->processScanResult($itemBrandScanResult, $normolizedItemName, $this->brands);

// if (is_null($itemBrand) && (
//     (mb_strpos($normolizedItemName, "fabulous") !== false) ||
//     (mb_strpos($normolizedItemName, "boudicсa") !== false) ||
//     (mb_strpos($normolizedItemName, "aeria") !== false) ||
//     (mb_strpos($normolizedItemName, "alchemico") !== false) ||
//     (mb_strpos($normolizedItemName, "daligramme") !== false)
// )) {
//     continue;
// }

// if (is_null($itemBrand)) {
//     exit;
// }
//                 // determine set
//                 $itemSetLineScanResult = $this->sacnStringForDictionaryValue($normolizedItemName, $this->brandSets[$itemBrand] ?? []);
//                 $itemSetLine = $this->processScanResult($itemSetLineScanResult, $normolizedItemName, $this->brandSets[$itemBrand] ?? []);

//                 if (!is_null($itemSetLine)) {

//                     echo "    brand: ", $itemBrand ?? "<unknown>", PHP_EOL;
//                     echo "    set line: ", $itemSetLine ?? "<unknown>", PHP_EOL;
//                     $item = new ItemSet(
//                         originalValue: $r[$indexTitle],
//                         provider: $fileData["file_name"],
//                         brand: $itemBrand,
//                         line: $itemSetLine,
//                     );
//                 } else {
//                     // determine brand line
//                     $itemLine = null;
//                     if (!is_null($itemBrand)) {
//                         $itemLineScanResult = $this->sacnStringForDictionaryValue($normolizedItemName, $this->brandLines[$itemBrand] ?? []);
//                         $itemLine = $this->processScanResult($itemLineScanResult, $normolizedItemName, $this->brandLines[$itemBrand] ?? []);
//                     }

//                     // determine volume
//                     $itemVolumeScanResult = $this->sacnStringForDictionaryValue($normolizedItemName, $this->volumes);
//                     $itemVolume = $this->processScanResult($itemVolumeScanResult, $normolizedItemName, $this->volumes);

//                     // determine type
//                     $itemTypeScanResult = $this->sacnStringForDictionaryValue($normolizedItemName, $this->types);
//                     $itemType = $this->processScanResult($itemTypeScanResult, $normolizedItemName, $this->types);

//                     // is artisan bottling
//                     $itemArtisanalBottlinScanResult = $this->sacnStringForDictionaryValue($normolizedItemName, $this->artisanalBottlingFlags);
//                     $itemIsArtisanalBottling = $this->processScanResult($itemArtisanalBottlinScanResult, $normolizedItemName);

//                     // is merking
//                     $itemMarkingScanResult = $this->sacnStringForDictionaryValue($normolizedItemName, $this->markingFlags);
//                     $itemHasMarking = $this->processScanResult($itemMarkingScanResult, $normolizedItemName);

//                     // is tester
//                     $itemIsTesterScanResult = $this->sacnStringForDictionaryValue($normolizedItemName, $this->testerFlags);
//                     $itemIsTester = $this->processScanResult($itemIsTesterScanResult, $normolizedItemName);

//                     // is sample
//                     $itemIsSampleScanResult = $this->sacnStringForDictionaryValue($normolizedItemName, $this->sampleFlags);
//                     $itemIsSample = $this->processScanResult($itemIsSampleScanResult, $normolizedItemName);

//                     // is old design
//                     $itemIsOldDesignScanResult = $this->sacnStringForDictionaryValue($normolizedItemName, $this->oldDesignFlags);
//                     $itemIsOldDesign = $this->processScanResult($itemIsOldDesignScanResult, $normolizedItemName);

//                     // sex
//                     $itemSexScanResult = $this->sacnStringForDictionaryValue($normolizedItemName, $this->sex);
//                     $itemSex = $this->processScanResult($itemSexScanResult, $normolizedItemName, $this->sex);

//                     // is refill
//                     $itemIsRefillScanResult = $this->sacnStringForDictionaryValue($normolizedItemName, $this->refillFlags);
//                     $itemIsRefill = $this->processScanResult($itemIsRefillScanResult, $normolizedItemName);

//                     // is damaged
//                     $itemIsDamagedScanResult = $this->sacnStringForDictionaryValue($normolizedItemName, $this->damageFlags);
//                     $itemIsDamaged = $this->processScanResult($itemIsDamagedScanResult, $normolizedItemName);

//                     echo "    brand: ", $itemBrand ?? "<unknown>", PHP_EOL;
//                     echo "    line: ", $itemLine ?? "<unknown>", PHP_EOL;
//                     echo "    volume: ", $itemVolume ?? "<unknown>", PHP_EOL;
//                     echo "    type: ", $itemType ?? "<unknown>", PHP_EOL;
//                     echo "    artisanal bottling: ", $itemIsArtisanalBottling ? "YES" : "NO", PHP_EOL;
//                     echo "    has marking: ", $itemHasMarking ? "YES" : "NO", PHP_EOL;
//                     echo "    is tester: ", $itemIsTester ? "YES" : "NO", PHP_EOL;
//                     echo "    is sample: ", $itemIsSample ? "YES" : "NO", PHP_EOL;
//                     echo "    is old design: ", $itemIsOldDesign ? "YES" : "NO", PHP_EOL;
//                     echo "    sex: ", $itemSex ?? "<unknown>", PHP_EOL;
//                     echo "    is refill: ", $itemIsRefill ? "YES" : "NO", PHP_EOL;
//                     echo "    is damaged: ", $itemIsDamaged ? "YES" : "NO", PHP_EOL;
//                     echo "    provider: ", $fileData["file_name"], PHP_EOL;

//                     $item = new Item(
//                         originalValue: $r[$indexTitle],
//                         provider: $fileData["file_name"],
//                         brand: $itemBrand,
//                         line: $itemLine,
//                         volume: $itemVolume,
//                         type: $itemType,
//                         sex: $itemSex,
//                         isArtisanalBottling: $itemIsArtisanalBottling,
//                         hasMarking: $itemHasMarking,
//                         isTester: $itemIsTester,
//                         isSample: $itemIsSample,
//                         isOldDesign: $itemIsOldDesign,
//                         isRefill: $itemIsRefill,
//                         isDamaged: $itemIsDamaged,
//                     );

//                     if (is_null($itemLine)) {
//                         $finalItemName = trim($normolizedItemName);
//                         if (mb_strlen($finalItemName) > 0) {
//                             $b[$itemBrand][$finalItemName] = 1;
//                         }
//                     }
//                 }
//                 $title = $this->generateTitle($item);
//                 $data[$itemBrand][$title][] = $item;
//             }
//         }
//         foreach ($b as $brand => $items) {
//             if (!isset($this->brandLines[$brand])) {
//                 echo "\"", $brand, "\" => [", PHP_EOL;
//                 foreach (array_keys($items) as $item) {
//                     echo "    \"", $item, "\" => \"" . ucwords($item) . "\",",PHP_EOL;
//                 }
//                 echo "],", PHP_EOL;
//             }
//         }

        // $this->writeResult($data);
        echo "done", PHP_EOL;
    }

    private function generateTitle(PriceListItemEntity|PriceListItemSetEntity $item): string
    {
        if ($item instanceof PriceListItemSetEntity) {
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

    private function processScanResult(?ScanResultEntity $result, string &$itemTitle, ?array $dictionary = null): null|bool|string
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

    private function sacnStringForDictionaryValue(string $targetString, array $dictionary, array $stopPhrases = []): ?ScanResultEntity
    {
        $targetStringSize = mb_strlen($targetString);
        if (array_is_list($dictionary)) {
            foreach ($dictionary as $dictionaryValue) {
                $dictionaryValueSize = mb_strlen($dictionaryValue);

                // if the stgings are equal
                if (($dictionaryValueSize === $targetStringSize) && ($targetString === $dictionaryValue)) {
                    return new ScanResultEntity($dictionaryValue, SubStringPositionEnum::Match);
                }

                // they aren't equal and the search one is longer; early exit
                if ($dictionaryValueSize >= $targetStringSize) {
                    continue;
                }

                $position = $this->findSubStringPosition($targetString, $dictionaryValue);
                if (!is_null($position)) {
                    return new ScanResultEntity($dictionaryValue, $position);
                }
            }
        } else {
            foreach ($dictionary as $dictionaryValue => $unifiedValue) {
                $dictionaryValueSize = mb_strlen($dictionaryValue);

                // if the stgings are equal
                if (($dictionaryValueSize === $targetStringSize) && ($targetString === $dictionaryValue)) {
                    return new ScanResultEntity($dictionaryValue, SubStringPositionEnum::Match);
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

                    return new ScanResultEntity($dictionaryValue, $position);
                }
            }
        }

        return null;
    }

    private function findSubStringPosition(string $haystack, string $needle): ?SubStringPositionEnum
    {
        /**
         * Spaces are very important because of business rules of creating price lists
         *
         * Firstly, let's check if the search string + space is located in the beginning of the haystack
         */
        $needleRightSpace = $needle . " ";
        if (mb_substr($haystack, 0, mb_strlen($needleRightSpace)) === $needleRightSpace) {
            return SubStringPositionEnum::Beginning;
        }

        // Secondly, let's check if space + the search string is located in the end of the haystack
        $needleLeftSpace = " " . $needle;
        if (mb_substr($haystack, -1 * mb_strlen($needleLeftSpace)) === $needleLeftSpace) {
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

    // private function normolizeString(string $string): string
    // {
    //     $string = mb_strtolower($string);

    //     /**
    //      * little data hacks
    //      * TODO: possible multibyte issue; replace str_replace function
    //      */
    //     $string = str_replace(" ", " ", $string);
    //     $string = preg_replace('/\s{2,}/', " ", $string);

    //     $string = str_replace("ml отливант5", "5ml отливант", $string);
    //     $string = str_replace(" mltest", " ml test", $string);

    //     // brand - line order normalization
    //     $string = str_replace("пакет ajmal crafting memories", "ajmal crafting memories пакет", $string);
    //     $string = str_replace("пакет ajmal signature", "ajmal signature пакет", $string);
    //     $string = str_replace("пакет philly phill", "philly phill пакет", $string);
    //     $string = str_replace("пакет - guerlain", "guerlain пакет", $string);
    //     $string = str_replace("пакет - il profvmo", "il profvmo пакет", $string);
    //     $string = str_replace("пакет - jo malone", "jo malone пакет", $string);
    //     $string = str_replace("пакет - kilian большой", "kilian пакет большой", $string);
    //     $string = str_replace("пакет - l artisan", "l artisan пакет", $string);
    //     $string = str_replace("пакет - laurent mazzone lm", "laurent mazzone lm пакет", $string);
    //     $string = str_replace("пакет - oros", "oros пакет", $string);
    //     $string = str_replace("пакет - wood incense", "wood incense пакет", $string);

    //     return $string;
    // }
}