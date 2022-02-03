<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ApiTrait;

class PoraboteController extends Controller
{
    function event()
    {

        $event = new \Porabote\Event\Event();
        debug($event);

    }

    function getData()
    {
        
    }

}
