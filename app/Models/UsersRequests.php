<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersRequests extends Model
{
    protected $connection = 'auth_mysql';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'sender_id',
        'token',
        'date_request',
        'date_confirm',
        'account_id',
    ];

    public function user()
    {
        return $this->belongsTo(ApiUsers::class);
    }

    public function sender()
    {
        return $this->belongsTo(ApiUsers::class);
    }

}