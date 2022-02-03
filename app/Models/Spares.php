<?php

namespace App\Models;

use App\Observers\AuthObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spares extends Model
{
    use HasFactory;

    protected $connection = 'api_mysql';
    public static $limit = 50;

    protected $fillable = [
        "name",
        "repair_date",
        "repair_id",
        "created_at",
        "description",
        "vendor_code",
        "quantity",
        "store_id",
        "user_id",
        "equipment_id",
    ];

    public static function boot() {
        parent::boot();
        Spares::observe(AuthObserver::class);
    }

    public function user()
    {
        return $this->belongsTo(ApiUsers::class);
    }

    public function equipments()
    {
        return $this->belongsTo(Equipments::class, 'equipment_id', 'id');
    }

    public function repairs()
    {
        return $this->belongsTo(Repairs::class, 'repair_id', 'id');
    }

    public function files()
    {
        return $this->hasMany(File::class, 'record_id', 'id' )->where('model_alias', '=', 'spares');
    }

    public function history()
    {
        return $this->hasMany(History::class, 'record_id', 'id' )->where('model_alias', '=', 'spares');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'record_id', 'id' )->where('class_name', '=', 'spares')->orderBy('parent_id');
    }

}
