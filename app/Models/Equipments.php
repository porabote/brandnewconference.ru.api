<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Observers\AuthObserver;

class Equipments extends Model
{
    protected $connection = 'api_mysql';
    public static $limit = 50;

    protected $fillable = [
        'name',
        'factory_number',
        'factory_name',
        'inventory_number',
        'brand_name',
        'sap_number',
        'vin_code',
        'gos_number',
        'release_date',
        'operation_start',
        'operation_end',
        'object_id',
        'organizations_own_id',
        'platform_id',
        'type_id',
        'engine_hours',
    ];

    public static function boot() {
        parent::boot();
        Equipments::observe(AuthObserver::class);
    }

    public function files()
    {
        return $this->hasMany(File::class, 'record_id', 'id' )
            ->where('model_alias', '=', 'equipments')
            ->where('flag', '=', 'on');
    }

    public function equipment_accidents()
    {
        return $this->hasMany(EquipmentAccidents::class, 'equipment_id', 'id' );
    }

    public function equipment_repairs()
    {
        return $this->hasMany(EquipmentsRepairs::class, 'equipment_id', 'id' );
    }

    public function history()
    {
        return $this->hasMany(History::class, 'record_id', 'id' )
            ->where('model_alias', '=', 'equipments')
            ->orderByDesc('id');;
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'record_id', 'id' )
            ->where('class_name', '=', 'equipments')
            ->orderBy('parent_id')
            ->orderByDesc('id');
    }

    public function organizations_own()
    {
        return $this->belongsTo(OrganizationsOwn::class, 'organizations_own_id', 'id' );
    }

    public function type()
    {
        return $this->belongsTo(EquipmentsTypes::class, 'type_id', 'id' );
    }

    public function platform()
    {
        return $this->belongsTo(Platforms::class, 'platform_id', 'id' );
    }

    public function object()
    {
        return $this->belongsTo(Objects::class, 'object_id', 'id' );
    }

    public function user()
    {
        return $this->belongsTo(ApiUsers::class, 'user_id', 'id' );
    }

    public function status()
    {
        return $this->belongsTo(Statuses::class, 'status_id', 'id' );
    }

    public function status_reason()
    {
        return $this->belongsTo(Statuses::class, 'status_reason_id', 'id' );
    }

}