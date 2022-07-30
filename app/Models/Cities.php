<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cities extends Model
{
    public static $limit = 5000;
    public $timestamps = false;
    protected $connection = 'dicts_mysql';

    protected $fillable = [
        'code',
        'country_code',
        'name_ru',
        'name_en',
    ];

}