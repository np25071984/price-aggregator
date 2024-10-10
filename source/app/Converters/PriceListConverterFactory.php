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
            case PriceListProviderEnum::NichePerfumeRu:
                return new NichePerfumeRuConverter();
            case PriceListProviderEnum::NichePerfumeUsd:
                return new NichePerfumeUsdConverter();
            case PriceListProviderEnum::AllScentUsd:
                return new AllScentUsdConverter();
            default:
                return new PriceListConverter(
                    id: PriceListProviderEnum::KurzinaRu,
                    indexArticle: 0,
                    indexTitle: 1,
                    indexPrice: 3,
                    firstRow: 2,
                );
                // throw new RuntimeException("Couldn't create config for provider");
        }
    }
}