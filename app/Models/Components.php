<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Porabote\Auth\Auth;

class Components extends Model
{

    function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->connection = 'api_mysql';
    }

    protected $hidden = [
        'model',
        'controller',
    ];

}