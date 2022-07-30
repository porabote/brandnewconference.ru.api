<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consumers extends Model
{

    protected $fillable = [
        'name',
        'last_name',
        'patronymic',
        'company_name',
        'post_name',
        'email',
        'phone',
        'part_type',
        'user_id',
    ];
    
    static public $requiredFields = [
        'name',
        'last_name',
        'company_name',
        'post_name',
        'email',
        'part_type',
    ];

}