<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Speakers;
use Porabote\FullRestApi\Server\ApiTrait;
use Porabote\Uploader\Uploader;

class SpeakersController extends Controller
{
    use ApiTrait;

    function create(Request $request)
    {
        $data = $request->all();

        if (!isset($data['id'])) {
            $record = Speakers::create($data);

        } else {
            $record = Speakers::find($data['id']);
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

        $record = Speakers::find($data['id']);

        foreach ($data as $field => $value) {
            if (array_key_exists($field, $record->getAttributes())) $record->$field = $value;
        }

        $record->update();

        return response()->json([
            'data' => $record,
            'meta' => []
        ]);
    }

    function delete($request)
    {
        $data = $request->all();
        $record = Speakers::find($data['id']);
        $record->delete();

        return response()->json([
            'data' => $record->toArray(),
            'meta' => []
        ]);
    }

    function resort($request)
    {
        $data = $request->all();
       // debug($data);
        $record = Speakers::where('lft', $data['lft'])->get()->first();
        
        if ($data['delta'] > 0) {
            $bool = $record->down($data['delta']);
        } else {
            $bool = $record->up(abs($data['delta']));
        }

        $record->save();

        return response()->json([
            'data' => $record->toArray(),
            'meta' => []
        ]);

    }

}