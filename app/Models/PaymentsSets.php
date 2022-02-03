<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Porabote\Auth\Auth;

class PaymentsSets extends Model
{
    use HasFactory;

    protected $table = 'payments_sets';
    public static $limit = 50;

    function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->connection = Auth::$user->account_alias . '_mysql';
    }
    
    public function files()
    {
        return $this->hasMany(File::class, 'record_id', 'id' )->where('model_alias','=', 'payments-sets');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'record_id', 'id' )->where('class_name', '=', 'payments-sets')->orderBy('parent_id');
    }

    public function history()
    {
        return $this->hasMany(History::class, 'record_id', 'id' )->where('model_alias', '=', 'payments-sets');
    }

    public function payments()
    {
        return $this->hasMany(Payments::class, 'payments_set_id', 'id' );
    }
}
