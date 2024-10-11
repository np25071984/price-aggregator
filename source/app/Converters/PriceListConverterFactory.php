<?php

namespace App\Converters;

use App\Enums\PriceListProviderEnum;
use RuntimeException;
use App\Converters\ConverterInterface;

class PriceListConverterFactory
{
    public function getConverter(PriceListProviderEnum $providerType): ConverterInterface
    {
        switch ($providerType) {
            case PriceListProviderEnum::Price1310Usd:
                return new Price1310UsdConverter();
            case PriceListProviderEnum::NichePerfumeUsd:
                return new NichePerfumeUsdConverter();
            case PriceListProviderEnum::AllScentUsd:
                return new AllScentUsdConverter();
            case PriceListProviderEnum::KurzinaUsd:
                return new KurzinaUsdConverter();
            case PriceListProviderEnum::PricePRCUsd:
                return new PricePRCUsdConverter();
            case PriceListProviderEnum::PriceParfumUsd:
                return new PriceParfumUsdConverter();
            case PriceListProviderEnum::PafumStockUsd:
                return new ParfumStockUsdConverter();

            case PriceListProviderEnum::BeautyPerfumeUsd:
                return new BeautyPerfumeUsdConverter();

            case PriceListProviderEnum::Unknown:
                throw new RuntimeException("Couldn't create config for provider");
        }
    }
}