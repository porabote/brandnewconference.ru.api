<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Porabote\FullRestApi\Server\ApiTrait;
use App\Models\Objects;

class ObjectsController extends Controller
{
    use ApiTrait;

    function add(Request $request)
    {
        $data = $request->all();

        if (!isset($data['id'])) {
            Objects::create($data);
        } else {
            $record = Objects::find($data['id']);
            $dataBefore = $record->getAttributes();

            foreach ($data as $fieldName => $value) {
                if (array_key_exists($fieldName, $dataBefore)) {
                    if ($value == "null") $value = NULL;
                    $record[$fieldName] = $value;
                }
            }
            $record->update();
        }

        return response()->json([
            'data' => $data,
            'meta' => []
        ]);
    }

}
