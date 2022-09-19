<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Porabote\FullRestApi\Server\ApiTrait;
use App\Models\Questionnaires;

class QuestionnairesController extends Controller
{
    use ApiTrait;

    function create(Request $request)
    {
        $data = $request->all();

        if (!isset($data['id'])) {
            $record = Questionnaires::create($data);

        } else {
            $record = Questionnaires::find($data['id']);
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

        $record = Questionnaires::find($data['id']);

        foreach ($data as $field => $value) {
            if (array_key_exists($field, $record->getAttributes())) $record->$field = $value;
        }

        $record->update();

        return response()->json([
            'data' => $record,
            'meta' => []
        ]);
    }

    function saveActives($request)
    {
        $data = $request->all();

        $questions = Questionnaires::get();

        foreach ($questions as $question) {
            $question->active_flg = isset($data['questions'][$question->id]) ? 1 : 0;
            $question->save();
        }
        return response()->json([
            'data' => [],
            'meta' => []
        ]);
    }
}
