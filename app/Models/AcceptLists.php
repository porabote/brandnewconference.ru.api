<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcceptLists extends Model
{
    protected $connection = 'api_mysql';
    public $timestamps = false;

    public function stepsDefault()
    {
        return $this->hasMany(AcceptListsStepsDefault::class, 'accept_list_id', 'id' );
    }

}