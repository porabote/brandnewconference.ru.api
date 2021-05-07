<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\ApiTrait;

class DepartmentsController extends Controller
{
    use ApiTrait;

    function get(Request $request)
    {
        $query = DB::table(strtolower($this->getModelName()));

        return response()->json([
            'data' => $query->limit(50)->get(),
            'meta' => []
        ]);


    }
}
