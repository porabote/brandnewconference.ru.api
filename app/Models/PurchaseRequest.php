<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Observers\AuthObserver;
use Porabote\Auth\Auth;

class PurchaseRequest extends Model
{
    public static $limit = 50;
    protected $table = 'purchase_request';
    public $timestamps = false;

    public static function boot() {
        parent::boot();
        Reports::observe(AuthObserver::class);
    }

    function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->connection = Auth::$user->account_alias . '_mysql';
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'record_id', 'id' )
            ->whereIn('class_name', ['App.PurchaseRequest', 'purchase-request'])
            ->orderBy('parent_id')
            ->orderByDesc('id');
    }

    public function object()
    {
        return $this->belongsTo(ObjectsLocal::class, 'object_id', 'id' );
    }

    public function initator()
    {
        return $this->belongsTo(Posts::class, 'initator_id', 'id' );
    }

    public function user()
    {
        return $this->belongsTo(Posts::class, 'user_id', 'id' );
    }

    public function status()
    {
        return $this->belongsTo(Statuses::class, 'status_id', 'id' );
    }

}