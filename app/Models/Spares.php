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
        "unit",
        "store_id",
        "user_id",
        "equipment_id",
        "spares_type_id",
    ];

    public static function boot() {
        parent::boot();
        Spares::observe(AuthObserver::class);
    }

    public function user()
    {
        return $this->belongsTo(ApiUsers::class);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipments::class, 'equipment_id', 'id');
    }

    public function store()
    {
        return $this->belongsTo(Objects::class, 'store_id', 'id');
    }

    public function repairs()
    {
        return $this->belongsTo(EquipmentsRepairs::class, 'repair_id', 'id');
    }

    public function status()
    {
        return $this->belongsTo(Statuses::class, 'status_id', 'id' );
    }

    public function spares_type()
    {
        return $this->belongsTo(SparesTypes::class, 'spares_type_id', 'id');
    }

    public function files()
    {
        return $this->hasMany(File::class, 'record_id', 'id' )->where('model_alias', '=', 'spares');
    }

    public function remains()
    {
        return $this->hasMany(SparesRemains::class, 'spare_id', 'id' )->orderByDesc('id');
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
