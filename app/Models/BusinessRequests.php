<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Porabote\Auth\Auth;

class BusinessRequests extends Model
{
    use HasFactory;

    public $timestamps = false;


    function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->connection = Auth::$user->account_alias . '_mysql';
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'record_id', 'id' )
            ->whereIn('class_name', ['App.BusinessRequests', 'BusinessRequests'])
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
        return $this->belongsTo(Posts::class, 'post_id', 'id' );
    }

    public function status()
    {
        return $this->belongsTo(Statuses::class, 'status_id', 'id' );
    }

    public function bill()
    {
        return $this->belongsTo(Bills::class, 'record_id', 'id' );
            //->where('business_requests.className', 'Store.Bills');
    }

}