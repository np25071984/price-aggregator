<?php

namespace App\Enums;

enum PriceListProviderEnum
{
    case NichePerfumeRu;
    case NichePerfumeUsd;
    case KurzinaRu;
    case KurzinaUsd;
    case AllScentRu;
    case AllScentUsd;
    case BeautyPerfumeRu;
    case Unknown;
}