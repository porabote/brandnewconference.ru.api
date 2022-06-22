<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Porabote\Auth\Auth;

class Departments extends Model
{
    protected $connection = 'dicts_mysql';

    protected $fillable = [
        'name',
        'company_id',
        'label',
        'code',
        'account_id',
        'local_id'
    ];

    public function account()
    {
        return $this->belongsTo(Accounts::class, 'account_id', 'id');
    }
}