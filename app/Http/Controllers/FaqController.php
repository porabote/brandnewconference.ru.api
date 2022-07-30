<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Faq;
use Porabote\FullRestApi\Server\ApiTrait;
use Porabote\Uploader\Uploader;

class FaqController extends Controller
{
    use ApiTrait;

    function create(Request $request)
    {
        $data = $request->all();

        if (!isset($data['id'])) {
            $record = Faq::create($data);

        } else {
            $record = Faq::find($data['id']);
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

        $record = Faq::find($data['id']);

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