<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Shifts;
use App\Models\ApiUsers;
use Porabote\FullRestApi\Server\ApiTrait;

class ShiftsController extends Controller
{
    use ApiTrait;

    function create(Request $request)
    {
        $data = $request->all();

        if (isset($data['id']) && $data['id']) {

            $record = Shifts::find($data['id']);

            foreach ($data as $fieldName => $value) {
                if (isset($record[$fieldName])) {
                    $record[$fieldName] = $value;
                }
            }
            $record->update();

        } else {
            debug($data);
            $record = Shifts::create($data);
        }

        return response()->json([
            'data' => $record->toArray(),
            'meta' => []
        ]);
    }

    function attachUsers($request)
    {
        $data = $request->all();
        $users = ApiUsers::whereIn('id', $data['user_ids'])->get();

        foreach ($users as $user) {
            $user->shift_id = $data['shift_id'];
            $user->update();
        }

        return response()->json([
            'data' => $users->toArray(),
            'meta' => []
        ]);
    }

    function detachUser($request, $id)
    {
        $data = $request->all();
        $user = ApiUsers::find($id);

        $user->shift_id = null;
        $user->update();

        return response()->json([
            'data' => $user->toArray(),
            'meta' => []
        ]);
    }

    function savePeriods($request)
    {
        $data = $request->all();

        $shift = Shifts::find($data['id']);
        $shift->periods = json_encode($data['periods']);
        $shift->update();

        return response()->json([
            'data' => $shift->toArray(),
            'meta' => []
        ]);
    }
}