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

//            $dict->$assoc_table = [
//                'data' => []
//            ];
            foreach ($dictData as $subDict) {
                //$item = new \App\Http\Responses\RestDataItem($subDict, $dict->attributes->assoc_table, '');
                $dict->list[$subDict->id] = $subDict;
                //array_push($dict->$assoc_table['data'], $item);
            }

        }

        return $Response;
    }
}
