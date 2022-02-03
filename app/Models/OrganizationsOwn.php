<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Observers\AuthObserver;

class OrganizationsOwn extends Model
{
    protected $connection = 'api_mysql';
    protected $table = 'organizations_own';

    public static $limit = 50;

    public function departments()
    {
        return $this->belongsTo(Departments::class, 'department_id', 'id' );
    }

}