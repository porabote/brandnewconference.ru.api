<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class AclAcos extends Model
{
    protected $connection = 'auth_mysql';
    protected $table = 'acl_acos';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'parent_id',
        'foreign_key',
        'model',
        'link',
    ];

}