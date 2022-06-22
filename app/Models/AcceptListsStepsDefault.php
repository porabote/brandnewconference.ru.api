<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Porabote\Auth\Auth;

class AcceptListsStepsDefault extends Model
{
    protected $connection = 'api_mysql';
    protected $table = 'accept_lists_steps_default';
    public $timestamps = false;

    public function default_users()
    {
        $account_id = Auth::$user->account_id;
        return $this->hasMany(AcceptListsStepsUsersDefault::class, 'accept_list_step_id', 'id' )
            ->where('account_id', '=', Auth::$user->account_id);
    }

}