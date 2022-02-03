<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Porabote\FullRestApi\Server\ApiTrait;

class DictsController extends Controller
{
    use ApiTrait;

    function getCallback($payload)
    {
        foreach($payload->data as &$dict) {

            $dictData = $dict->attributes['namespace']::get()->toArray();
            $dict->type = $dict->attributes['assoc_table'];

            foreach ($dictData as $subDict) {
                $dict->data[$subDict['id']] = $subDict;
            }

        }

        return $payload;
    }
}
