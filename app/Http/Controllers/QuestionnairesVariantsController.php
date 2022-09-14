<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Porabote\FullRestApi\Server\ApiTrait;
use App\Models\QuestionnairesVariants;

class QuestionnairesVariantsController extends Controller
{
    use ApiTrait;

    function create(Request $request)
    {
        $data = $request->all();

        if (!isset($data['id'])) {
            $record = QuestionnairesVariants::create($data);

        } else {
            $record = QuestionnairesVariants::find($data['id']);
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

        $record = QuestionnairesVariants::find($data['id']);

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
