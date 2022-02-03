<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessEvents extends Model
{
    protected $connection = 'api_mysql';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'business_events';

    protected $fillable = [
        'component_id',
        'name'
    ];

    public function component()
    {
        return $this->belongsTo(Components::class, 'component_id', 'id' );
    }
}