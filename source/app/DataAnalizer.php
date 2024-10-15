<?php

namespace App;

use App\Enums\PriceListProviderEnum;
use App\Entities\Products\AbstractProductEntity;
use App\Entities\Products\PerfumeEntity;
use App\Entities\Products\BagEntity;
use App\Entities\Products\CandleEntity;
use App\Entities\Products\ShampooAndGelEntity;
use App\Entities\Products\UnknownProductEntity;
use App\Entities\RawPriceListItem;
use App\Entities\ScanResultEntity;
use App\Entities\ScanResultFullEntity;
use App\Enums\SubStringPositionEnum;

readonly class DataAnalizer
{
    private array $bags;
    private array $brands;
    private array $brandStopPhrases;
    private array $perfumeTypes;
    private array $volumes;
    private array $testerFlags;
    private array $sampleFlags;
    private array $oldDesignFlags;
    private array $artisanalBottlingFlags;
    private array $markingFlags;
    private array $brandLines;
    private array $brandSets;
    private array $sex;
    private array $damageFlags;
    private array $refillFlags;

    public function __construct()
    {
        $this->bags = include __DIR__ . "/../dictionaries/productTypes/bags.php";

        // we want to sort all associative dictionaries by key length to avoid false hits
        $brands = include __DIR__ . "/../dictionaries/brands.php";
        uksort($brands, function($a, $b) {
            return mb_strlen($b) <=> mb_strlen($a);
        });
        $this->brands = $brands;
        $this->brandStopPhrases = include __DIR__ . "/../dictionaries/brandStopPhrases.php";
        $this->perfumeTypes = include __DIR__ . "/../dictionaries/perfumeTypes.php";
        $this->volumes = include __DIR__ . "/../dictionaries/volumes.php";
        $this->testerFlags = include __DIR__ . "/../dictionaries/testerFlags.php";
        $this->sampleFlags = include __DIR__ . "/../dictionaries/sampleFlags.php";
        $this->oldDesignFlags = include __DIR__ . "/../dictionaries/oldDesignFlags.php";
        $this->artisanalBottlingFlags = include __DIR__ . "/../dictionaries/artisanalBottlingFlags.php";
        $this->markingFlags = include __DIR__ . "/../dictionaries/markingFlags.php";
        $this->damageFlags = include __DIR__ . "/../dictionaries/damageFlags.php";
        $this->refillFlags = include __DIR__ . "/../dictionaries/refillFlags.php";
        $this->sex = include __DIR__ . "/../dictionaries/sex.php";

        // $brandLines = include __DIR__ . "/../dictionaries/brandLines.php";
        // $brandLinesSorted = [];
        // foreach($brandLines as $brand => $items) {
        //     uksort($items, function($a, $b) {
        //         return mb_strlen($b) <=> mb_strlen($a);
        //     });
        //     $brandLinesSorted[$brand] = $items;
        // }
        // unset($brandLines);
        // $this->brandLines = $brandLinesSorted;

        // $brandSets = include __DIR__ . "/../dictionaries/brandSets.php";
        // $brandSetsSorted = [];
        // foreach($brandSets as $brand => $items) {
        //     uksort($items, function($a, $b) {
        //         return mb_strlen($b) <=> mb_strlen($a);
        //     });
        //     $brandSetsSorted[$brand] = $items;
        // }
        // unset($brandSets);
        // $this->brandSets = $brandSetsSorted;
    }

    /**
     * @return AbstractProductEntity[]
     */
    public function analyze(array $rawPriceData, PriceListProviderEnum $dataProvider): array
    {
        $data = [];
        /** @var RawPriceListItem $row */
        foreach ($rawPriceData as $row) {
            $title = $row->title;

            // determine if a bag
            $isBagScanResult = $this->sacnStringForListValues($title, $this->bags);
            if (!is_null($isBagScanResult)) {
                $data[] = new BagEntity(
                    article: $row->article,
                    originalTitle: $row->title,
                    price: $row->price,
                    provider: $dataProvider,
                );

                continue;
            }

            // determine if a candle
            $isCandleScanResult = $this->sacnStringForListValues($title, ["свеча", "candle"]);
            if (!is_null($isCandleScanResult)) {
                $data[] = new CandleEntity(
                    article: $row->article,
                    originalTitle: $row->title,
                    price: $row->price,
                    provider: $dataProvider,
                );

                continue;
            }

            // determine if a shampoo & gel
            $isShGelScanResult = $this->sacnStringForListValues($title, ["sh/gel", "sh/g"]);
            if (!is_null($isShGelScanResult)) {
                $data[] = new ShampooAndGelEntity(
                    article: $row->article,
                    originalTitle: $row->title,
                    price: $row->price,
                    provider: $dataProvider,
                );

                continue;
            }

            // determine type
            $perfumeType = null;
            $perfumeTypeScanResult = $this->sacnStringForDictionaryValues($title, $this->perfumeTypes);
            if (is_null($perfumeTypeScanResult)) {
                $data[] = new UnknownProductEntity(
                    article: $row->article,
                    originalTitle: $row->title,
                    price: $row->price,
                    provider: $dataProvider,
                );

                continue;
            }
            $title = $this->removeResultFromString($perfumeTypeScanResult, $title);
            $perfumeType = $perfumeTypeScanResult->unifiedValue;

            // determine brand
            $brand = null;
            $brandScanResult = $this->sacnStringForDictionaryValues($title, $this->brands, $this->brandStopPhrases);
            if (!is_null($brandScanResult)) {
                $title = $this->removeResultFromString($brandScanResult, $title);
                $brand = $brandScanResult->unifiedValue;
            }

            // determine volume
            $volume = null;
            $volumeScanResult = $this->sacnStringForDictionaryValues($title, $this->volumes);
            if (!is_null($volumeScanResult)) {
                $title = $this->removeResultFromString($volumeScanResult, $title);
                $volume = $volumeScanResult->unifiedValue;
            }

            // determine if tester
            $isTester = false;
            $isTesterScanResult = $this->sacnStringForListValues($title, $this->testerFlags);
            if (!is_null($isTesterScanResult)) {
                $title = $this->removeResultFromString($isTesterScanResult, $title);
                $isTester = true;
            }

            // determine if sample
            $isSample = false;
            $isSampleScanResult = $this->sacnStringForListValues($title, $this->sampleFlags);
            if (!is_null($isSampleScanResult)) {
                $title = $this->removeResultFromString($isSampleScanResult, $title);
                $isSample = true;
            }

            // determine if old design
            $isOldDesign = false;
            $isOldDesignScanResult = $this->sacnStringForListValues($title, $this->oldDesignFlags);
            if (!is_null($isOldDesignScanResult)) {
                $title = $this->removeResultFromString($isOldDesignScanResult, $title);
                $isOldDesign = true;
            }

            // determine if artisanal bottling
            $isArtisanalBottling = false;
            $isArtisanalBottlingScanResult = $this->sacnStringForListValues($title, $this->artisanalBottlingFlags);
            if (!is_null($isArtisanalBottlingScanResult)) {
                $title = $this->removeResultFromString($isArtisanalBottlingScanResult, $title);
                $isArtisanalBottling = true;
            }

            // determine if marking
            $hasMarking = false;
            $hasMarkingScanResult = $this->sacnStringForListValues($title, $this->markingFlags);
            if (!is_null($hasMarkingScanResult)) {
                $title = $this->removeResultFromString($hasMarkingScanResult, $title);
                $hasMarking = true;
            }

            // determine if refill
            $isRefill = false;
            $isRefillScanResult = $this->sacnStringForListValues($title, $this->refillFlags);
            if (!is_null($isRefillScanResult)) {
                $title = $this->removeResultFromString($isRefillScanResult, $title);
                $isRefill = true;
            }

            // determine if damaged
            $isDamaged = false;
            $isDamagedScanResult = $this->sacnStringForListValues($title, $this->damageFlags);
            if (!is_null($isDamagedScanResult)) {
                $title = $this->removeResultFromString($isDamagedScanResult, $title);
                $isDamaged = true;
            }

            // determine the sex
            $sex = null;
            $sexScanResult = $this->sacnStringForDictionaryValues($title, $this->sex);
            if (!is_null($sexScanResult)) {
                $title = $this->removeResultFromString($sexScanResult, $title);
                $sex = $sexScanResult->unifiedValue;
            }

            $data[] = new PerfumeEntity(
                article: $row->article,
                originalTitle: $row->title,
                price: $row->price,
                provider: $dataProvider,
                brand: $brand ?? "<unknown_brand>",
                line: "",
                volume: $volume ?? "<unknown_volume>",
                type: $perfumeType ?? "<unknown_type>",
                sex: $sex ?? "<unknown_sex>",
                isArtisanalBottling: $isArtisanalBottling,
                hasMarking: $hasMarking,
                isTester: $isTester,
                isSample: $isSample,
                isOldDesign: $isOldDesign,
                isRefill: $isRefill,
                isDamaged: $isDamaged,
            );
        }
        return $data;
    }

    private function sacnStringForListValues(string $haystack, array $list): ?ScanResultEntity
    {
        $haystackSize = mb_strlen($haystack);
        foreach ($list as $listValue) {
            $listValueSize = mb_strlen($listValue);

            // if the stgings are equal
            if (($listValueSize === $haystackSize) && ($haystack === $listValue)) {
                return new ScanResultEntity($listValueSize, SubStringPositionEnum::Match);
            }

            // they aren't equal and the search one is longer; early exit
            if ($listValueSize >= $haystackSize) {
                continue;
            }

            $position = $this->findSubStringPosition($haystack, $listValue);
            if (!is_null($position)) {
                return new ScanResultEntity($listValue, $position);
            }
        }

        return null;
    }

    private function sacnStringForDictionaryValues(string $haystack, array $dictionary, array $stopPhrases = []): ?ScanResultFullEntity
    {
        $haystackSize = mb_strlen($haystack);
        foreach ($dictionary as $dictionaryValue => $unifiedValue) {
            $dictionaryValueSize = mb_strlen($dictionaryValue);

            // if the stgings are equal
            if (($dictionaryValueSize === $haystackSize) && ($haystack === $dictionaryValue)) {
                return new ScanResultFullEntity(
                    $dictionaryValue,
                    SubStringPositionEnum::Match,
                    $unifiedValue
                );
            }

            // they aren't equal and the search one is longer; early exit
            if ($dictionaryValueSize >= $haystackSize) {
                continue;
            }

            $position = $this->findSubStringPosition($haystack, $dictionaryValue);
            if (!is_null($position)) {
                if ($this->isInStopList($haystack, $unifiedValue, $stopPhrases)) {
                    continue;
                }

                return new ScanResultFullEntity($dictionaryValue, $position, $unifiedValue);
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

        // neither in the beginning nor end; has to be surrounded by two spaces
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

    private function removeResultFromString(ScanResultEntity $result, string $string): string
    {
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
        return preg_replace($pattern, "", $string, 1);
    }
}