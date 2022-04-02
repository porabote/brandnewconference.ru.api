<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DialogsUsers extends Model
{
    public $timestamps = false;
    protected $connection = 'chat_mysql';

    protected $fillable = [
        'dialog_id',
        'user_id',
    ];

}