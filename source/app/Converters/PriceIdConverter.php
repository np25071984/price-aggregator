<?php

namespace App\Converters;

use App\Enums\PriceListProviderEnum;

final readonly class PriceIdConverter
{
    public function convert(PriceListProviderEnum $id): string
    {
        return match($id) {
            PriceListProviderEnum::DePerfumesUsd => "ДеПарфюмс доллар США",
            PriceListProviderEnum::NichePerfumeUsd => "НишеПарфюм доллар США",
            PriceListProviderEnum::AvangardUsd => "Авангард доллар США",
            PriceListProviderEnum::BeliyUsd => "Белый доллар США",
            PriceListProviderEnum::GevorgUsd => "Геворг доллар США",
            PriceListProviderEnum::GuldenRub => "Гульден Руб",
            PriceListProviderEnum::ZurabUsd => "Зураб доллар США",
            PriceListProviderEnum::NashaFirmaUsd => "НашаФирма доллар США",
            PriceListProviderEnum::OrabelUsd => "Орабел доллар США",
            PriceListProviderEnum::RagimovaDianaUsd => "Рагимова доллар США",
            PriceListProviderEnum::KurzinaUsd => "Курзина доллар США",
            PriceListProviderEnum::KurzinaRub => "Курзина Руб",
            PriceListProviderEnum::StockUsd => "Сток доллар США",
            PriceListProviderEnum::FestivalRub => "Фестиваль Руб",
        };
    }
}