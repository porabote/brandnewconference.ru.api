<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Observers\AuthObserver;
use Porabote\Auth\Auth;

class SampleComponent extends Model
{
    //protected $connection = 'api_mysql';
    //protected $table = 'sample_component';

    public static $limit = 50;

    public static function boot() {
        parent::boot();
        SampleComponent::observe(AuthObserver::class);
    }

    function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->connection = Auth::$user->account_alias . '_mysql';
    }

    protected $fillable = [
        'id',
        'name',
        'type_id',
        'date_period',
        'object_id',
        'comment'
    ];

    public function files()
    {
        return $this->hasMany(File::class, 'record_id', 'id' )->where('model_alias', '=', 'sample_component');
    }

    public function history()
    {
        return $this->hasMany(History::class, 'record_id', 'id' )->where('model_alias', '=', 'sample_component');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'record_id', 'id' )
            ->where('class_name', '=', 'sample_component')
            ->orderBy('parent_id');
    }

    public function departments()
    {
        return $this->belongsTo(Departments::class, 'department_id', 'id' );
    }

    public function object()
    {
        return $this->belongsTo(Departments::class, 'object_id', 'id' );
    }

    public function user()
    {
        return $this->belongsTo(ApiUsers::class, 'user_id', 'id' );
    }

    public function types()
    {
        return $this->belongsTo(ReportTypes::class, 'type_id', 'id' );
    }

}