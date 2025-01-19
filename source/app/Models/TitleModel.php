<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TitleModel extends Model
{
    use HasFactory;

    protected $table = 'titles';
    // public $incrementing = false;
    protected $fillable = ['title', 'brand_id'];
    protected $connection = 'pgsql';
    public $timestamps = false;
}
