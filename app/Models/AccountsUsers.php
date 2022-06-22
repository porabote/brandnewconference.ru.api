<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class AccountsUsers extends Model
{
    protected $connection = 'auth_mysql';
    public $timestamps = false;

    protected $fillable = [
        'account_id',
        'user_id',
    ];

}