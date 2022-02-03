<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $connection = 'api_mysql';

    use HasFactory;

    protected $fillable = [
        'name',
        'file_path',
        'basename',
        'ext',
        'uri',
        'path',
        'user_id',
        'mime',
        'token',
        'width',
        'height',
        'flag',
        'title',
        'dscr',
        'label',
        'main',
        'model_alias',
        'record_id',
        'data_s_path'
    ];

}