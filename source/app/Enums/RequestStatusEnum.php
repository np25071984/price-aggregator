<?php

namespace App\Enums;

enum RequestStatusEnum: string
{
    case Uploading = "uploading";
    case Pending = "pending";
    case Processing = "processing";
    case Finished = "finished";
}