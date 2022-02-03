<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Platforms extends Model
{
    protected $connection = 'api_mysql';

    public function history()
    {
        return $this->hasMany(History::class, 'record_id', 'id' )->where('model_alias', '=', 'platforms');
    }

    public function objects()
    {
        return $this->hasMany(Objects::class, 'platform_id', 'id')
            ->orderBy('name');
    }

}