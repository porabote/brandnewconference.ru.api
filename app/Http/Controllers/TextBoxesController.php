<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TextBoxes;
use Porabote\FullRestApi\Server\ApiTrait;
use Porabote\Uploader\Uploader;

class TextBoxesController extends Controller
{
    use ApiTrait;

    function create(Request $request)
    {
        $data = $request->all();

        if (!isset($data['id'])) {
            $record = TextBoxes::create($data);

        } else {
            $record = TextBoxes::find($data['id']);
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

        $record = TextBoxes::find($data['id']);

        foreach ($data as $field => $value) {
            if (array_key_exists($field, $record->getAttributes())) $record->$field = $value;
        }

        $record->update();

        return response()->json([
            'data' => $record,
            'meta' => []
        ]);
    }
}