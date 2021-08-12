<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Reports extends Model
{
    protected $connection = 'api_mysql';

    public function files()
    {
        return $this->hasMany(File::class, 'record_id', 'id' );
    }

    public function departments()
    {
        return $this->belongsTo(Departments::class, 'department_id', 'id' );
    }

    public function types()
    {
        return $this->belongsTo(ReportTypes::class, 'type_id', 'id' );
    }

}