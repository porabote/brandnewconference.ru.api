<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentsSets extends Model
{
    use HasFactory;

    protected $connection = 'norilsk_mysql';
    protected $table = 'payments_sets';

    public function files()
    {
        return $this->hasMany(File::class, 'record_id', 'id' )->where('model_alias','=', 'payments-sets');;
    }
}
