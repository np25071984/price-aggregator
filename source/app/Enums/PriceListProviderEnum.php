<?php

namespace App\Enums;

enum PriceListProviderEnum: string
{
    case DePerfumesUsd = "DePerfumes";
    case NichePerfumeUsd = "NichePerfumeUsd";
    case AvangardUsd = "AvangardUsd";
    case BeliyUsd = "BeliyUsd";
    case GevorgUsd = "GevorgUsd";
    case GuldenRu = "GuldenRu";
    case ZurabUsd = "ZurabUsd";
    case NashaFirmaUsd = "NashaFirmaUsd";
    case OrabelUsd = "OrabelUsd";
    case RagimovaDianaUsd = "RagimovaDianaUsd";
    case KurzinaUsd = "KurzinaUsd";
    case StockUsd = "StockUsd";
    case Unknown = "Unknown";
}