<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ObserversDefault;
use Porabote\FullRestApi\Server\ApiTrait;

class ObserversDefaultController extends Controller
{

    use ApiTrait;

    function add(Request $request)
    {
        $data = $request->all();
        foreach ($data['users'] as $observer_id) {

            $user = ObserversDefault::where('user_id', '=', $observer_id)
                ->where('business_event_id', '=', $data['business_event_id'])->first();
            if ($user === null) {
                ObserversDefault::create([
                    'user_id' => $observer_id,
                    'business_event_id' => $data['business_event_id']
                ]);
            }
        }

        return response()->json([
            'data' => $data,
            'meta' => []
        ]);
    }

}