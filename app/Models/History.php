<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Observers\AuthObserver;
use Porabote\Auth\Auth as Auth;

class History extends Model
{
    protected $connection = 'api_mysql';

    public static function boot() {
        parent::boot();

        History::observe(AuthObserver::class);
        //parent::observe(new AuthObserver);
    }

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'created_at' => '',
        'updated_at' => '',
        'flag' => 'on',
//        'user_id' => '',
//        'user_name' => ''
    ];

    protected $fillable = [
        'model_alias',
        'record_id',
        'msg',
//        'user_id',
//        'user_name',
       // 'flag'
    ];

    public function setCreatedAtAttribute($value)
    {
        $this->attributes['created_at'] = date('Y-m-d H:i:s');
    }
    public function setUpdatedAtAttribute($value)
    {
        $this->attributes['updated_at'] = date('Y-m-d H:i:s');
    }

//    public function setUserIdAttribute($value)
//    {
//        $this->attributes['user_id'] = Auth::getUser('id');
//    }

//    public function setUserNameAttribute($value)
//    {
//        $this->attributes['user_name'] = Auth::getUser('name');
//    }

    
}