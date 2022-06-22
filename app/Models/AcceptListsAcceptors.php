<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AcceptListsAcceptors extends Model
{
    protected $connection = 'api_mysql';
    protected $table = 'accept_lists_acceptors';
    public $timestamps = false;

    protected $fillable = [
        'account_id',
        'user_id',
        'step_id',
    ];

    function api_user()
    {
        return $this->belongsTo(ApiUsers::class, 'user_id', 'id' );
    }


}