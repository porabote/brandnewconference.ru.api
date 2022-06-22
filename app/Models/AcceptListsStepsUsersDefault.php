<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcceptListsStepsUsersDefault extends Model
{
    protected $connection = 'api_mysql';
    protected $table = 'accept_lists_users_default';
    public $timestamps = false;

    public function api_user()
    {
        return $this->belongsTo(ApiUsers::class, 'user_id', 'id' );
    }

}