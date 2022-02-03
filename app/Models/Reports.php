<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Observers\AuthObserver;

class Reports extends Model
{
    protected $connection = 'api_mysql';

    public static $limit = 50;

    public static function boot() {
        parent::boot();
        Reports::observe(AuthObserver::class);
    }

    protected $fillable = [
        'id',
        'type_id',
        'date_period',
        'object_id',
        'comment'
    ];

    public function files()
    {
        return $this->hasMany(File::class, 'record_id', 'id' )->where('model_alias', '=', 'reports');
    }

    public function history()
    {
        return $this->hasMany(History::class, 'record_id', 'id' )->where('model_alias', '=', 'reports');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'record_id', 'id' )
            ->where('class_name', '=', 'reports')
            ->orderBy('parent_id')
            ->orderByDesc('id');
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