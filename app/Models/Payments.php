<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Porabote\Auth\Auth;

class Payments extends Model
{
    use HasFactory;

    protected $table = 'payments';
    
    public $timestamps = false;

    protected $fillable = [
        'status_id'
    ];

    function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->connection = Auth::$user->account_alias . '_mysql';
    }

    public function bill()
    {
        return $this->belongsTo(Bills::class, 'bill_id', 'id' );
    }

    public function contractor()
    {
        return $this->belongsTo(Contractors::class, 'contractor_id', 'id' );
    }

    public function client()
    {
        return $this->belongsTo(Contractors::class, 'client_id', 'id' );
    }

    public function object()
    {
        return $this->belongsTo(Departments::class, 'object_id', 'id' );
    }

    public function status()
    {
        return $this->belongsTo(Statuses::class, 'status_id', 'id' );
    }

}
