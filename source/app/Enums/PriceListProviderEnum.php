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
    case ZubarUsd = "ZubarUsd";
    case NashaFirmaUsd = "NashaFirmaUsd";
    case OrabelUsd = "OrabelUsd";
    case RagimovaDianaUsd = "RagimovaDianaUsd";
    case KurzinaUsd = "KurzinaUsd";
    case Unknown = "Unknown";
}