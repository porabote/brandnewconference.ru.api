<?php

namespace App\Http\Components;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\History;
use Porabote\Auth\Auth;

class HistoryComponent
{

    public static function write($data = [])
    {

     //   debug(Auth::getUser());

//
//        $data['user_id'] = Auth::getUser('id');
//        $data['user_name'] = Auth::getUser('name');
        
        History::create(array_merge($data, []));
    }
}