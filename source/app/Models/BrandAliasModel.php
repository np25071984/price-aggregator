<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrandAliasModel extends Model
{
    use HasFactory;

    protected $table = 'brand-alias';
    protected $fillable = ['brand_id', 'alias', 'size', 'stop-words'];
    protected $connection = 'pgsql';
    public $timestamps = false;
}
