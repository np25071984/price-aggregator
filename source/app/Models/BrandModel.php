<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrandModel extends Model
{
    use HasFactory;

    protected $table = 'brands';
    // public $incrementing = false;
    protected $fillable = ['name'];
    protected $connection = 'pgsql';
    public $timestamps = false;
}
