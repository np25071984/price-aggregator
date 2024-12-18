<?php

namespace App\Enums;

enum RequestTypeEnum: string
{
    case Aggregation = 'aggregation';
    case Merge = 'merge';
}