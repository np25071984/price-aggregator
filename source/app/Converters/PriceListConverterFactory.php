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
            case PriceListProviderEnum::DePerfumesUsd:
                return new DePerfumesConverter();
            case PriceListProviderEnum::NichePerfumeUsd:
                return new NichePerfumeUsdConverter();
            case PriceListProviderEnum::AvangardUsd:
                return new AvangardUsdConverter();
            case PriceListProviderEnum::BeliyUsd:
                return new BeliyUsdConverter();
            case PriceListProviderEnum::GevorgUsd:
                return new GevorgUsdConverter();
            case PriceListProviderEnum::GuldenRu:
                return new GuldenRuConverter();
            case PriceListProviderEnum::ZurabUsd:
                return new ZurabUsdConverter();
            case PriceListProviderEnum::NashaFirmaUsd:
                return new NashaFirmaUsdConverter();
            case PriceListProviderEnum::OrabelUsd:
                return new OrabelUsdConverter();
            case PriceListProviderEnum::RagimovaDianaUsd:
                return new RagimovaDianaUsdConverter();
            case PriceListProviderEnum::KurzinaUsd:
                return new KurzinaUsdConverter();
            case PriceListProviderEnum::StockUsd:
                return new StockUsdConverter();
            case PriceListProviderEnum::Unknown:
                throw new RuntimeException("Couldn't create config for provider " . $providerType->value);
        }
    }
}