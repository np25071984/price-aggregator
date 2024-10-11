<?php

namespace App\Enums;

enum PriceListProviderEnum: string
{
    case Price1310Usd = "Price1310Usd";
    case PricePRCUsd = "PricePRCUsd";
    case NichePerfumeUsd = "NichePerfumeUsd";
    case PriceParfumUsd = "PriceParfumUsd";
    case KurzinaUsd = "KurzinaUsd";
    case PafumStockUsd = "ParfumStockUsd";
    case AllScentUsd = "AllScentUsd";
    case BeautyPerfumeUsd = "BeautyPerfumeUsd";
    case Unknown = "Unknown";
}