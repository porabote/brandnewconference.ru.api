<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessListsUsers extends Model
{
    protected $connection = 'api_mysql';
    protected $table = 'access_lists_users';
    public $timestamps = false;

    public function api_user()
    {
        return $this->belongsTo(ApiUsers::class, 'user_id', 'id' );
    }

}