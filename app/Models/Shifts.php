<?php

namespace App\Models;

use App\Observers\AuthObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Observers\HistoryObserver;

class Shifts extends Model
{
    use HasFactory;

    protected $connection = 'auth_mysql';
    public $timestamps = false;
    public static $limit = 50;

    public static function boot() {
        parent::boot();
        Shifts::observe(HistoryObserver::class);
    }

    protected $fillable = [
        'title',
        'head_user_id',
        'platform_id',
    ];

    function platform() {
        return $this->belongsTo(Platforms::class, 'platform_id', 'id' );
    }

    function head_user() {
        return $this->belongsTo(ApiUsers::class, 'head_user_id', 'id' );
    }

    function users() {
        return $this->hasMany(ApiUsers::class, 'shift_id', 'id' );
    }

}