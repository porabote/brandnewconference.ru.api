<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Spares;
use App\Models\File;
use Porabote\FullRestApi\Server\ApiTrait;
use Porabote\Uploader\Uploader;
use App\Models\History;
use App\Models\Comment;
use App\Models\Config;

class SparesController extends Controller
{
    use ApiTrait;

    function add(Request $request)
    {
        $data = $request->all();

        $record = Spares::create($data);

        History::create([
            'model_alias' => 'spares',
            'record_id' => $record->id,
            'msg' => 'Добавлена новая запись'
        ]);

        return response()->json([
            'data' => $record,
            'meta' => []
        ]);
    }

    function addComment(Request $request)
    {
        $data = $request->all();

        Comment::create($data);

        $msgData = $data;
        $msgData['record'] = $this->getById($data['record_id']);

        $message = new Message();
        $message->setData($msgData)->setTemplateById(1);

        Mailer::setToByEventId([188888], $data['record_id']);
        Mailer::send($message);

        return response()->json([
            'data' => $data,
            'meta' => []
        ]);
    }

    function getById($id)
    {
        $record = Spares::find($id);
        $data = $record->getAttributes();
        $data['user'] = $record->user->getAttributes();
        $data['repairs'] = $record->repairs->getAttributes();
        $data['equipments'] = $record->equipments->getAttributes();
        return $data;
    }
}