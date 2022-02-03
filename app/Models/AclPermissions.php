<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Porabote\Auth\Auth;

class AclPermissions extends Model
{
    protected $table = 'acl_permissions';
    public $timestamps = false;

    protected $fillable = [
        "aro_id",
        "aco_id",
        "_create",
        "_read",
        "_update",
        "_delete",
    ];

    function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->connection = Auth::$user->account_alias . '_mysql';
    }

}