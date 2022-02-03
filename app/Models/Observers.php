<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Porabote\Auth\Auth;

class Observers extends Model
{
    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'observers';

    protected $fillable = [
        'entity_id',
        'user_id',
        'business_event_id'
    ];

    function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->connection = Auth::$user->account_alias . '_mysql';
    }

    public function user()
    {
        return $this->belongsTo(ApiUsers::class, 'user_id', 'id' );
    }

    public function event()
    {
        return $this->belongsTo(BusinessEvents::class, 'business_event_id', 'id' );
    }

}