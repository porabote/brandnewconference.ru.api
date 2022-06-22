<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AcceptListsSteps extends Model
{
    protected $connection = 'api_mysql';
    public $timestamps = false;

    protected $fillable = [
        'foreign_key',
        'lft',
        'model',
        'account_id',
        'step_default_id',
    ];

    function default_step()
    {
        return $this->belongsTo(AcceptListsStepsDefault::class, 'step_default_id', 'id' );
    }

    function acceptor()
    {
        return $this->hasOne(AcceptListsAcceptors::class, 'step_id', 'id' );
    }

}