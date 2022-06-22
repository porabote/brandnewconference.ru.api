<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Accounts extends Model
{
    protected $connection = 'auth_mysql';
    public $timestamps = false;

}