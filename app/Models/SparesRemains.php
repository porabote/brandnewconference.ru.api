<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Observers\AuthObserver;

class SparesRemains extends Model
{
    protected $connection = 'api_mysql';

    public static function boot() {
        parent::boot();
        SparesRemains::observe(AuthObserver::class);
    }

    protected $fillable = [
        "comment",
        "remain",
        "spare_id",
    ];

    protected $attributes = [
        'user_name' => ''
    ];

}