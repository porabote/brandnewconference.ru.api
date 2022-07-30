<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Porabote\Auth\Auth;

class Files extends Model
{
    use HasFactory;

    public $timestamps = false;
    
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

    protected $hidden = [
        'path',
    ];

    public function user()
    {
        return $this->belongsTo(Users::class);
    }

}