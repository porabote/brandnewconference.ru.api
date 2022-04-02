<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Objects extends Model
{
    protected $connection = 'api_mysql';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'address',
        'kind',
        "platform_id",
        "parent_id"
    ];

    public function departments()
    {
        return $this->hasMany(Departments::class, 'department_id', 'id' )
            ->orderBy('name');
    }

    public function platform()
    {
        return $this->belongsTo(Platforms::class, 'platform_id', 'id' );
    }

    public function parent()
    {
        return $this->belongsTo(Objects::class, 'parent_id', 'id' );
    }

}