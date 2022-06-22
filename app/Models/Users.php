<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Porabote\Auth\Auth;

class Users extends Model
{
    use HasFactory, Notifiable;

    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'people_id',
        'name',
        'last_name',
        'confirm',
        'token',
        'post_id',
        'api_id',
        'password',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'token'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
//    protected $casts = [
//        'created',
//        'modified'
//    ];

    function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->connection = Auth::$user->account_alias . '_mysql';
    }

    public function post()
    {
        return $this->belongsTo(Posts::class, 'post_id', 'id' );
    }

    public function api_user()
    {
        return $this->belongsTo(ApiUsers::class, 'api_id', 'id' );
    }

    public function avatar()
    {
        return $this->hasOne(Files::class, 'record_id', 'id' )
            ->where('model_alias', 'Users')
            ->where('label', 'main');
    }

    public function aro()
    {
        return $this->hasOne(AclAros::class, 'foreign_key', 'id' )
            ->where('label', 'User');
    }

    public function users_requests()
    {
        return $this->hasMany(UsersRequests::class, 'user_id', 'id' );
    }

}
