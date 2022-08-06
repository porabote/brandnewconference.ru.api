<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Hashes extends Model
{

    protected $fillable = [
        'hash',
        'part_format',
    ];

}