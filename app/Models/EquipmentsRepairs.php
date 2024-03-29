<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Observers\AuthObserver;

class EquipmentsRepairs extends Model
{
    protected $connection = 'api_mysql';
    protected $table = 'equipments_repairs';
    public static $limit = 50;

    protected $fillable = [
        "equipment_id",
        "type",
        "name",
        "engine_hours",
        "date_at",
        "date_to",
        "downtime",
        "desc",
        "desc_short",
        "doer_id",
    ];

    public static function boot() {
        parent::boot();
        EquipmentsRepairs::observe(AuthObserver::class);
    }

    public function user()
    {
        return $this->belongsTo(ApiUsers::class, 'user_id', 'id' );
    }

    public function doer()
    {
        return $this->belongsTo(ApiUsers::class, 'doer_id', 'id' );
    }

    public function spares()
    {
        return $this->hasMany(EquipmentsRepairsSpares::class, 'repair_id', 'id' );
    }

}