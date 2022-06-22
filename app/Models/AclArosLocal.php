<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Porabote\Auth\Auth;

class AclArosLocal extends Model
{
    //protected $connection = 'Thyssen_mysql';
    protected $table = 'acl_aros';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'parent_id',
        'label',
        'foreign_key',
        'model',
    ];

    function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->connection = 'auth_mysql';
    }

    public function permissions()
    {
        return $this->hasMany(AclPermissions::class, 'aro_id', 'id' );
    }

}