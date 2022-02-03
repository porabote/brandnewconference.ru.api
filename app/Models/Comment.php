<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Observers\AuthObserver;
use Porabote\Auth\Auth;

class Comment extends Model
{
    public $timestamps = false;

    public static function boot() {
        parent::boot();
        Comment::observe(AuthObserver::class);
    }

    protected $attributes = [
        'date_created' => null
    ];

    protected $fillable = [
        'msg',
        'parent_id',
        'record_id',
        'class_name',
        'date_created'
    ];

    function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->connection = Auth::$user->account_alias . '_mysql';
    }

    public function files()
    {
        return $this->hasMany(File::class, 'record_id', 'id' )->where('model_alias', '=', 'comment');
    }

    public function user()
    {
        return $this->belongsTo(ApiUsers::class, 'user_id', 'id' );
    }

    public function setDateCreatedAttribute($value)
    {
        $this->attributes['date_created'] = date("Y-m-d H:i:s");
    }


}