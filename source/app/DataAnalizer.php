<?php

namespace App;

use App\Enums\PriceListProviderEnum;
use App\Entities\AbstractPriceListItemEntity;
use App\Entities\PriceListItemEntity;
use App\Entities\RawPriceListItem;
use App\Entities\ScanResultEntity;
use App\Enums\SubStringPositionEnum;

readonly class DataAnalizer
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

    public function __construct()
    {
        // we want to sort all associative dictionaries by key length to avoid false hits
        $brands = include __DIR__ . "/../dictionaries/brands.php";
        uksort($brands, function($a, $b) {
            return mb_strlen($b) <=> mb_strlen($a);
        });
        $this->brands = $brands;

        $brandLines = include __DIR__ . "/../dictionaries/brandLines.php";
        $brandLinesSorted = [];
        foreach($brandLines as $brand => $items) {
            uksort($items, function($a, $b) {
                return mb_strlen($b) <=> mb_strlen($a);
            });
            $brandLinesSorted[$brand] = $items;
        }
        unset($brandLines);
        $this->brandLines = $brandLinesSorted;

        $brandSets = include __DIR__ . "/../dictionaries/brandSets.php";
        $brandSetsSorted = [];
        foreach($brandSets as $brand => $items) {
            uksort($items, function($a, $b) {
                return mb_strlen($b) <=> mb_strlen($a);
            });
            $brandSetsSorted[$brand] = $items;
        }
        unset($brandSets);
        $this->brandSets = $brandSetsSorted;

        $this->brandStopPhrases = include __DIR__ . "/../dictionaries/brandStopPhrases.php";
        $this->volumes = include __DIR__ . "/../dictionaries/volumes.php";
        $this->types = include __DIR__ . "/../dictionaries/types.php";
        $this->testerFlags = include __DIR__ . "/../dictionaries/testerFlags.php";
        $this->sampleFlags = include __DIR__ . "/../dictionaries/sampleFlags.php";
        $this->oldDesignFlags = include __DIR__ . "/../dictionaries/oldDesignFlags.php";
        $this->artisanalBottlingFlags = include __DIR__ . "/../dictionaries/artisanalBottlingFlags.php";
        $this->markingFlags = include __DIR__ . "/../dictionaries/markingFlags.php";
        $this->sex = include __DIR__ . "/../dictionaries/sex.php";
        $this->damageFlags = include __DIR__ . "/../dictionaries/damageFlags.php";
        $this->refillFlags = include __DIR__ . "/../dictionaries/refillFlags.php";
        $this->fillerWords = include __DIR__ . "/../dictionaries/fillerWords.php";
    }

    /**
     * @return AbstractPriceListItemEntity[]
     */
    public function analyze(array $rawPriceData, PriceListProviderEnum $dataProvider): array
    {
        $data = [];
        /** @var RawPriceListItem $row */
        foreach ($rawPriceData as $row) {
            $title = $row->title;
            // determine brand
            $itemBrandScanResult = $this->sacnStringForDictionaryValue($title, $this->brands, $this->brandStopPhrases);
            $itemBrand = $this->processScanResult($itemBrandScanResult, $title, $this->brands);

            $data[] = new PriceListItemEntity(
                article: $row->article,
                originalTitle: $row->title,
                price: $row->price,
                provider: $dataProvider,
                brand: $itemBrand ?? "<unknown>",
                line: "",
                volume: 0.0,
                type: "",
                sex: "",
                isArtisanalBottling: false,
                hasMarking: false,
                isTester: false,
                isSample: false,
                isOldDesign: false,
                isRefill: false,
                isDamaged: false,
            );
        }
        return $data;
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
}