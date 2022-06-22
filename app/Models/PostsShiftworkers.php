<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Porabote\Auth\Auth;

class PostsShiftworkers extends Model
{
    //protected $connection = 'auth_mysql';
    public $timestamps = false;

    function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->connection = Auth::$user->account_alias . '_mysql';
    }

}