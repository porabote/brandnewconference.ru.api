<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Observers\AuthObserver;

class EquipmentAccidents extends Model
{
    protected $connection = 'api_mysql';
    protected $table = 'equipments_accidents';
    public static $limit = 50;

    protected $fillable = [
        "date",
        "act_number",
        "details",
        "reasons",
        "downtime",
        "measures",
        "equipment_id",
    ];

    public static function boot() {
        parent::boot();
        EquipmentAccidents::observe(AuthObserver::class);
    }

    public function files()
    {
        return $this->hasMany(File::class, 'record_id', 'id' )
            ->where('model_alias', '=', 'equipments')
            ->where('flag', '=', 'on');
    }

    public function user()
    {
        return $this->belongsTo(ApiUsers::class, 'user_id', 'id' );
    }

}