<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Porabote\FullRestApi\Server\ApiTrait;
use App\Models\Partners;

class PartnersController extends Controller
{
    use ApiTrait;

    function create(Request $request)
    {
        $data = $request->all();

        if (!isset($data['id'])) {
            $record = Partners::create($data);

        } else {
            $record = Partners::find($data['id']);
            $record->update();
        }

        return response()->json([
            'data' => $record,
            'meta' => []
        ]);
    }

    function edit($request)
    {
        $data = $request->all();

        if (!isset($data['active_flg'])) $data['active_flg'] = 0;

        $record = Partners::find($data['id']);

        foreach ($data as $field => $value) {
            if (array_key_exists($field, $record->getAttributes())) $record->$field = $value;
        }

        $record->update();

        return response()->json([
            'data' => $record,
            'meta' => []
        ]);
    }

    function resort($request)
    {
        $data = $request->all();

        $record = Partners::where('lft', $data['lft'])->get()->first();
        // $record::fixTree();exit();

        if ($data['delta'] < 0) {
            $bool = $record->up(abs($data['delta']));
        } else if ($data['delta'] > 0) {
            $bool = $record->down(abs($data['delta']));
        }

        $record->save();

        return response()->json([
            'data' => $record->toArray(),
            'meta' => []
        ]);

    }
}
