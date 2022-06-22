<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersShiftworkers extends Model
{
    protected $connection = 'auth_mysql';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'shiftworker_id',
    ];

}