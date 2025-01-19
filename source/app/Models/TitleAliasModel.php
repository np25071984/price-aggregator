<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TitleAliasModel extends Model
{
    use HasFactory;

    protected $table = 'title-alias';
    protected $fillable = ['title_id', 'alias', 'size'];
    protected $connection = 'pgsql';
    public $timestamps = false;
}
