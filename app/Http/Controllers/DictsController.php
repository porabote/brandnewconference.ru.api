<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\ApiTrait;

class DictsController extends Controller
{
    use ApiTrait;

    function getHandle($Response)
    {
        foreach($Response->data as &$dict) {

            $assoc_table = $dict->attributes->assoc_table;

            $dictData = DB::connection('dicts_mysql')->table($assoc_table)->get();

            foreach ($dictData as $subDict) {
                $dict->list[$subDict->id] = $subDict;
            }

        }

        return $Response;
    }
}
