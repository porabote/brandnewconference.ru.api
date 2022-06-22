<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Porabote\Auth\Auth;

class ObjectsLocal extends Model
{
    protected $connection = 'api_mysql';
    protected $table = "departments";
    public $timestamps = false;

    function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->connection = Auth::$user->account_alias . '_mysql';
    }

    public function departments()
    {
        return $this->hasMany(DepartmentsLegacy::class, 'department_id', 'id' )
            ->orderBy('name');
    }

}