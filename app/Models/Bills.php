<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Porabote\Auth\Auth;

class Bills extends Model
{
    use HasFactory;

    public $timestamps = false;

    public $table = 'contract_extantions';

    function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->connection = Auth::$user->account_alias . '_mysql';
    }

    public function object()
    {
        return $this->belongsTo(Departments::class, 'object_id', 'id' );
    }
//
//    public function initator()
//    {
//        return $this->belongsTo(Posts::class, 'initator_id', 'id' );
//    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'record_id', 'id' )
            ->whereIn('class_name', ['Store.Bills', 'bills'])
            ->orderBy('parent_id')
            ->orderByDesc('id');
    }

    public function user()
    {
        return $this->belongsTo(Posts::class, 'manager_id', 'id' );
    }

    public function status()
    {
        return $this->belongsTo(Statuses::class, 'status_id', 'id' );
    }

}
