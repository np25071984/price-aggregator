<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\RequestStatusEnum;
use App\Enums\RequestTypeEnum;

class RequestModel extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'requests';
    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['result', 'type', 'status', 'stats'];
    protected $connection = 'pgsql';

    protected $casts = [
        'status' => RequestStatusEnum::class,
        'type' => RequestTypeEnum::class,
    ];
}
