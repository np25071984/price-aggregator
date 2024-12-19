<?php

namespace App\Enums;

enum PriceListProviderEnum: string
{
    case DePerfumesUsd = "DePerfumes";
    case NichePerfumeUsd = "NichePerfumeUsd";
    case AvangardUsd = "AvangardUsd";
    case BeliyUsd = "BeliyUsd";
    case GevorgUsd = "GevorgUsd";
    case GuldenRub = "GuldenRub";
    case ZurabUsd = "ZurabUsd";
    case NashaFirmaUsd = "NashaFirmaUsd";
    case OrabelUsd = "OrabelUsd";
    case RagimovaDianaUsd = "RagimovaDianaUsd";
    case KurzinaUsd = "KurzinaUsd";
    case KurzinaRub = "KurzinaRub";
    case StockUsd = "StockUsd";
    case FestivalRub = "FestivalRub";
    case Unknown = "Unknown";
}