<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Observers\AuthObserver;

class EquipmentsRepairsSpares extends Model
{
    protected $connection = 'api_mysql';
    protected $table = 'equipments_repairs_spares';
    public $timestamps = false;
    public static $limit = 500;

    protected $fillable = [
        'count',
        'repair_id',
        'spare_id',
    ];

    public function spare()
    {
        return $this->belongsTo(Spares::class, 'spare_id', 'id' );
    }

}