<?php

namespace App;

use App\Enums\PriceListProviderEnum;
use App\Entities\Products\AbstractProductEntity;
use App\Entities\Products\PerfumeEntity;
use App\Entities\Products\BagEntity;
use App\Entities\Products\CandleEntity;
use App\Entities\Products\ShampooAndGelEntity;
use App\Entities\Products\BodyLotionEntity;
use App\Entities\Products\BodyOilEntity;
use App\Entities\Products\HandCreamEntity;
use App\Entities\Products\BodyCreamEntity;
use App\Entities\Products\BathCreamEntity;
use App\Entities\Products\CableEntity;
use App\Entities\Products\AtomiserEntity;
use App\Entities\Products\LaundryDetergentEntity;
use App\Entities\Products\DeoStickEntity;
use App\Entities\Products\OtherProductEntity;
use App\Entities\Products\SetEntity;
use App\Entities\Products\ShowerGelEntity;
use App\Entities\Products\SoapEntity;
use App\Entities\Products\UnknownProductEntity;
use App\Entities\RawPriceListItem;
use App\Entities\ScanResultEntity;
use App\Entities\ScanResultFullEntity;
use App\Enums\SubStringPositionEnum;
use App\Models\BrandAliasModel;
use App\Models\BrandModel;
use Illuminate\Support\Facades\DB;

readonly class DataAnalizer
{
    private array $bags;
    private array $others;
    private array $brands;
    private array $names;
    private array $brandStopPhrases;
    private array $perfumeTypes;
    private array $volumes;
    private array $testerFlags;
    private array $artisanalBottlingFlags;
    private array $markingFlags;
    private array $brandSets;
    private array $sex;
    private array $damageFlags;

    public function __construct()
    {
        $this->bags = include __DIR__ . "/../dictionaries/productTypes/bags.php";
        $this->others = include __DIR__ . "/../dictionaries/productTypes/others.php";

        // we want to sort all associative dictionaries by key length to avoid false hits
        $brands = include __DIR__ . "/../dictionaries/brands.php";
        uksort($brands, function($a, $b) {
            return mb_strlen($b) <=> mb_strlen($a);
        });
        $this->brands = $brands;

        $names = include __DIR__ . "/../dictionaries/names.php";
        $namesSorted = [];
        foreach($names as $brand => $brandNames) {
            uksort($brandNames, function($a, $b) {
                return mb_strlen($b) <=> mb_strlen($a);
            });
            $namesSorted[$brand] = $brandNames;
        }
        unset($names);
        $this->names = $namesSorted;

        $this->brandStopPhrases = include __DIR__ . "/../dictionaries/brandStopPhrases.php";
        $this->perfumeTypes = include __DIR__ . "/../dictionaries/perfumeTypes.php";
        $this->volumes = include __DIR__ . "/../dictionaries/volumes.php";
        $this->testerFlags = include __DIR__ . "/../dictionaries/testerFlags.php";
        $this->artisanalBottlingFlags = include __DIR__ . "/../dictionaries/artisanalBottlingFlags.php";
        $this->markingFlags = include __DIR__ . "/../dictionaries/markingFlags.php";
        $this->damageFlags = include __DIR__ . "/../dictionaries/damageFlags.php";
        $this->sex = include __DIR__ . "/../dictionaries/sex.php";

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
    }

    /**
     * @return AbstractProductEntity[]
     */
    public function analyze(array $rawPriceData, PriceListProviderEnum $dataProvider): array
    {
        $data = [];
        /** @var RawPriceListItem $row */
        foreach ($rawPriceData as $row) {
            $title = $row->normalizedTitle;

            // determine if a Bag
            $isBagScanResult = $this->scanStringForListValues($title, $this->bags);
            if (!is_null($isBagScanResult)) {
                $data[] = new BagEntity(
                    article: $row->article,
                    originalTitle: $row->originalTitle,
                    price: $row->price,
                    provider: $dataProvider,
                );

                continue;
            }

            // determine if a Candle
            $isCandleScanResult = $this->scanStringForListValues($title, ["свеча", "candle", "сandle"], ["свеча" => ["+35g. свеча"]]);
            if (!is_null($isCandleScanResult)) {
                $data[] = new CandleEntity(
                    article: $row->article,
                    originalTitle: $row->originalTitle,
                    price: $row->price,
                    provider: $dataProvider,
                );

                continue;
            }

            // determine if a Shampoo & Gel
            $isShGelScanResult = $this->scanStringForListValues($title, ["sh/gel", "sh/g"]);
            if (!is_null($isShGelScanResult)) {
                $data[] = new ShampooAndGelEntity(
                    article: $row->article,
                    originalTitle: $row->originalTitle,
                    price: $row->price,
                    provider: $dataProvider,
                );

                continue;
            }

            // determine if a Body Lotion
            $isBodyLotionScanResult = $this->scanStringForListValues($title, ["body lotion", "b/lotion"]);
            if (!is_null($isBodyLotionScanResult)) {
                $data[] = new BodyLotionEntity(
                    article: $row->article,
                    originalTitle: $row->originalTitle,
                    price: $row->price,
                    provider: $dataProvider,
                );

                continue;
            }

            // determine if a Body Oil
            $isBodyOilScanResult = $this->scanStringForListValues($title, ["body oil"]);
            if (!is_null($isBodyOilScanResult)) {
                $data[] = new BodyOilEntity(
                    article: $row->article,
                    originalTitle: $row->originalTitle,
                    price: $row->price,
                    provider: $dataProvider,
                );

                continue;
            }

            // determine if a Cable
            $isCableScanResult = $this->scanStringForListValues($title, ["шнур"]);
            if (!is_null($isCableScanResult)) {
                $data[] = new CableEntity(
                    article: $row->article,
                    originalTitle: $row->originalTitle,
                    price: $row->price,
                    provider: $dataProvider,
                );

                continue;
            }

            // determine if a Hand Cream
            $isHandCreamScanResult = $this->scanStringForListValues($title, ["hand cream", "крем для рук"]);
            if (!is_null($isHandCreamScanResult)) {
                $data[] = new HandCreamEntity(
                    article: $row->article,
                    originalTitle: $row->originalTitle,
                    price: $row->price,
                    provider: $dataProvider,
                );

                continue;
            }

            // determine if a Body Cream
            $isBodyCreamScanResult = $this->scanStringForListValues($title, ["body cream", "крем для тела"]);
            if (!is_null($isBodyCreamScanResult)) {
                $data[] = new BodyCreamEntity(
                    article: $row->article,
                    originalTitle: $row->originalTitle,
                    price: $row->price,
                    provider: $dataProvider,
                );

                continue;
            }

            // determine if a Bath Cream
            $isBathCreamScanResult = $this->scanStringForListValues($title, ["bath cream"]);
            if (!is_null($isBathCreamScanResult)) {
                $data[] = new BathCreamEntity(
                    article: $row->article,
                    originalTitle: $row->originalTitle,
                    price: $row->price,
                    provider: $dataProvider,
                );

                continue;
            }

            // determine if an Atomiser
            $isAtomiserScanResult = $this->scanStringForListValues($title, ["atomiser", "atomiseur", "атомайзер"], ["atomiser" => ["with atomiser"]]);
            if (!is_null($isAtomiserScanResult)) {
                $data[] = new AtomiserEntity(
                    article: $row->article,
                    originalTitle: $row->originalTitle,
                    price: $row->price,
                    provider: $dataProvider,
                );

                continue;
            }

            // determine if an Hand Soap
            $isSoapScanResult = $this->scanStringForListValues($title, ["hand and body soap", "hand soap", "hand&body soap", "liquide soap", "жидкое мыло", "soap", "мыло"]);
            if (!is_null($isSoapScanResult)) {
                $data[] = new SoapEntity(
                    article: $row->article,
                    originalTitle: $row->originalTitle,
                    price: $row->price,
                    provider: $dataProvider,
                );

                continue;
            }

            // determine if an Laundry Detergent
            $isLaundryDetergentScanResult = $this->scanStringForListValues($title, ["(парфюмированное моющее средство для стирки)", "парфюмированное моющее средство для стирки", "жидкий порошок", "laundry"]);
            if (!is_null($isLaundryDetergentScanResult)) {
                $data[] = new LaundryDetergentEntity(
                    article: $row->article,
                    originalTitle: $row->originalTitle,
                    price: $row->price,
                    provider: $dataProvider,
                );

                continue;
            }

            // determine if a Deo Stick
            $isDeoStockScanResult = $this->scanStringForListValues($title, ["deo stick"]);
            if (!is_null($isDeoStockScanResult)) {
                $data[] = new DeoStickEntity(
                    article: $row->article,
                    originalTitle: $row->originalTitle,
                    price: $row->price,
                    provider: $dataProvider,
                );

                continue;
            }

            // determine if a Shower Gel
            $isShowerGelScanResult = $this->scanStringForListValues($title, ["shower gel"]);
            if (!is_null($isShowerGelScanResult)) {
                $data[] = new ShowerGelEntity(
                    article: $row->article,
                    originalTitle: $row->originalTitle,
                    price: $row->price,
                    provider: $dataProvider,
                );

                continue;
            }

            // determine if Other category
            $isOtherCategorycanResult = $this->scanStringForListValues($title, $this->others);
            if (!is_null($isOtherCategorycanResult)) {
                $data[] = new OtherProductEntity(
                    article: $row->article,
                    originalTitle: $row->originalTitle,
                    price: $row->price,
                    provider: $dataProvider,
                );

                continue;
            }

            // determine brand
            $brand = null;
            $brandScanResult = $this->sacnStringForDictionaryValues($title, $this->brands, $this->brandStopPhrases);
            if (!is_null($brandScanResult)) {
                $title = $this->removeResultFromString($brandScanResult, $title);
                $brand = $brandScanResult->unifiedValue;
            }

            if (is_null($brand)) {
                $data[] = new UnknownProductEntity(
                    article: $row->article,
                    originalTitle: $row->originalTitle,
                    price: $row->price,
                    provider: $dataProvider,
                );

                continue;
            }

            // determine if set
            $setScanResult = $this->sacnStringForDictionaryValues($title, $this->brandSets[$brand] ?? []);
            if (!is_null($setScanResult)) {
                $data[] = new SetEntity(
                    article: $row->article,
                    originalTitle: $row->originalTitle,
                    price: $row->price,
                    provider: $dataProvider,
                    brand: $brand,
                    line: $setScanResult->unifiedValue,
                );

                continue;
            }

            $name = null;
            $nameScanResult = $this->sacnStringForDictionaryValues($title, $this->names[$brand] ?? []);
            if (!is_null($nameScanResult)) {
                $title = $this->removeResultFromString($nameScanResult, $title);
                $name = $nameScanResult->unifiedValue;
            }

            // determine perfume type
            $perfumeType = "perfume";
            $perfumeTypeScanResult = $this->sacnStringForDictionaryValues($title, $this->perfumeTypes);
            if (!is_null($perfumeTypeScanResult)) {
                $title = $this->removeResultFromString($perfumeTypeScanResult, $title);
                $perfumeType = $perfumeTypeScanResult->unifiedValue;
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
            $isTesterScanResult = $this->scanStringForListValues($title, $this->testerFlags);
            if (!is_null($isTesterScanResult)) {
                $title = $this->removeResultFromString($isTesterScanResult, $title);
                $isTester = true;
            }

            // determine if sample
            $isSample = false;
            $isSampleScanResult = $this->scanStringForListValues($title, ["sample", "пробник", "(пробник)"]);
            if (!is_null($isSampleScanResult)) {
                $title = $this->removeResultFromString($isSampleScanResult, $title);
                $isSample = true;
            }

            // determine if old design
            $isOldDesign = false;
            $isOldDesignScanResult = $this->scanStringForListValues($title, ["старый дизайн", "old design"]);
            if (!is_null($isOldDesignScanResult)) {
                $title = $this->removeResultFromString($isOldDesignScanResult, $title);
                $isOldDesign = true;
            }

            // determine if artisanal bottling
            $isArtisanalBottling = false;
            $isArtisanalBottlingScanResult = $this->scanStringForListValues($title, $this->artisanalBottlingFlags);
            if (!is_null($isArtisanalBottlingScanResult)) {
                $title = $this->removeResultFromString($isArtisanalBottlingScanResult, $title);
                $isArtisanalBottling = true;
            }

            // determine if marking
            $hasMarking = false;
            $hasMarkingScanResult = $this->scanStringForListValues($title, $this->markingFlags);
            if (!is_null($hasMarkingScanResult)) {
                $title = $this->removeResultFromString($hasMarkingScanResult, $title);
                $hasMarking = true;
            }

            // determine if refill
            $isRefill = false;
            $isRefillScanResult = $this->scanStringForListValues($title, [
                "refil",
                "refill",
                "рефилл",
                "(refill)"
            ]);
            if (!is_null($isRefillScanResult)) {
                $title = $this->removeResultFromString($isRefillScanResult, $title);
                $isRefill = true;
            }

            // determine if damaged
            $isDamaged = false;
            $isDamagedScanResult = $this->scanStringForListValues($title, $this->damageFlags);
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

            // determine if limited
            $isLimited = false;
            $isLimitedScanResult = $this->scanStringForListValues($title, ["limited", "(limited"]);
            if (!is_null($isLimitedScanResult)) {
                $title = $this->removeResultFromString($isLimitedScanResult, $title);
                $isLimited = true;
            }

            // determine hasCap
            $hasCap = null;
            $hasCapScanResult = $this->scanStringForListValues($title, [
                    "(с крышкой)",
                    "с крышкой)",
                    "с крышкой",
                    "с/кр",
                    "c/кр"
                ]
            );
            if (!is_null($hasCapScanResult)) {
                $title = $this->removeResultFromString($hasCapScanResult, $title);
                $hasCap = true;
            }

            if (is_null($hasCap)) {
                $hasNotCapScanResult = $this->scanStringForListValues($title, [
                    "без крышки",
                    "б/головы",
                ]);
                if (!is_null($hasNotCapScanResult)) {
                    $title = $this->removeResultFromString($hasNotCapScanResult, $title);
                    $hasCap = false;
                }
            }
            /**
             * We didn't find name but in some cases there isn't name at all. When, for example,
             * name and brand are equal. So, let's correct this case.
             */
            if (is_null($name) && mb_strlen($title) === 0) {
                $name = "";
            }

            $data[] = new PerfumeEntity(
                article: $row->article,
                originalTitle: $row->originalTitle,
                price: $row->price,
                provider: $dataProvider,
                brand: $brand,
                name: $name,
                volume: $volume,
                type: $perfumeType,
                sex: $sex,
                isLimited: $isLimited,
                hasCap: $hasCap,
                isArtisanalBottling: $isArtisanalBottling,
                hasMarking: $hasMarking,
                isTester: $isTester,
                isSample: $isSample,
                isOldDesign: $isOldDesign,
                isRefill: $isRefill,
                isDamaged: $isDamaged,
                comment: $title,
            );
        }
        return $data;
    }

    private function scanStringForListValues(string $haystack, array $list, array $stopList = []): ?ScanResultEntity
    {
        $haystackSize = mb_strlen($haystack);
        foreach ($list as $listValue) {
            $listValueSize = mb_strlen($listValue);

            // if the stgings are equal
            if (($listValueSize === $haystackSize) && ($haystack === $listValue)) {
                return new ScanResultEntity($listValue, SubStringPositionEnum::Match);
            }

            // they aren't equal and the search one is longer; early exit
            if ($listValueSize >= $haystackSize) {
                continue;
            }

            $position = $this->findSubStringPosition($haystack, $listValue);
            if (!is_null($position)) {
                if (isset($stopList[$listValue]) && !empty($stopList[$listValue])) {
                    $stopListScanResult = $this->scanStringForListValues($haystack, $stopList[$listValue]);
                    if (!is_null($stopListScanResult)) {
                        continue;
                    }
                }
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
        $res = null;
        switch ($result->positionInScannedString) {
            case SubStringPositionEnum::Match:
                $res = "";
            case SubStringPositionEnum::Beginning:
                $str = $result->dictionaryValue . " ";
                $res = mb_substr($string, mb_strlen($str));
                break;
            case SubStringPositionEnum::End:
                $str = " " . $result->dictionaryValue;
                $res = mb_substr($string, 0, (mb_strlen($string) - mb_strlen($str)));
                break;
            case SubStringPositionEnum::Middle:
                $str = " " . $result->dictionaryValue . " ";
                $pattern = "/" . preg_quote($str, "/") . "/";
                $res = preg_replace($pattern, " ", $string, 1);
                break;
        }

        return $res;
    }
}