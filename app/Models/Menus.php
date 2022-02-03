<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Menus extends Model
{
    protected $connection = 'auth_mysql';
    protected $table = 'menus';
    public $timestamps = false;

    protected $fillable = [
        'primary_key',
        'name',
        'link',
        "parent_id",
        "lft",
        "rght",
        "controller",
        "action",
        "plugin",
        "target",
        "flag",
        "aco_id",
    ];

}