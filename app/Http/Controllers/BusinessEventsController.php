<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Porabote\FullRestApi\Server\ApiTrait;
use App\Models\BusinessEvents;

class BusinessEventsController extends Controller
{
    use ApiTrait;

    function add(Request $request)
    {
        $data = $request->all();

        $record = BusinessEvents::create($data);

        return response()->json([
            'data' => $record,
            'meta' => []
        ]);
    }

    function getByComponentId($request, $id)
    {
        $events = BusinessEvents::where('component_id', $id)->get()->toArray();

        return response()->json([
            'data' => $events,
            'meta' => []
        ]);
    }

}