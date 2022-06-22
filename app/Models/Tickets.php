<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Observers\AuthObserver;
use App\Observers\HistoryObserver;
use Porabote\Auth\Auth;

class Tickets extends Model
{
    protected $connection = 'api_mysql';

    public static function boot() {
        parent::boot();
        Tickets::observe(HistoryObserver::class);
        Tickets::observe(AuthObserver::class);
    }

    protected $fillable = [
        'id',
        'type_id',
        'comment'
    ];

    public function user()
    {
        return $this->belongsTo(ApiUsers::class, 'user_id', 'id' );
    }

    public function status()
    {
        return $this->belongsTo(Statuses::class, 'status_id', 'id' );
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'record_id', 'id' )
            ->where('class_name', '=', 'tickets')
            ->orderBy('parent_id')
            ->orderByDesc('id');
    }

    public function type()
    {
        return $this->belongsTo(EquipmentsTypes::class, 'type_id', 'id' );
    }

    public function files()
    {
        return $this->hasMany(File::class, 'record_id', 'id' )
            ->where('model_alias', '=', 'tickets')
            ->where('flag', '=', 'on');
    }

    public function history()
    {
        return $this->hasMany(History::class, 'record_id', 'id' )
            ->where('model_alias', '=', 'tickets')
            ->orderByDesc('id');
    }

    public function steps()
    {
        return $this->hasMany(AcceptListsSteps::class, 'foreign_key')
            ->where('model', 'Tickets')
            ->where('account_id', Auth::$user->account_id)
            ->where('active', '=', 1)
            ->orderBy('lft');
    }

}