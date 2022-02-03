<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\File;
use Porabote\FullRestApi\Server\ApiTrait;
use Porabote\Uploader\Uploader;
use App\Models\History;
use App\Models\Statuses;
use App\Models\Comment;
use App\Models\Config;
use App\Models\Equipments;
use App\Models\EquipmentAccidents;
use App\Http\Components\Mailer\Mailer;
use App\Http\Components\Mailer\Message;
use App\Http\Controllers\ObserversController;
use Porabote\Auth\Auth;

class EquipmentsController extends Controller
{
    use ApiTrait;

    function add(Request $request)
    {
        $data = $request->all();

        if (isset($data['id']) && $data['id']) {


            $record = Equipments::find($data['id']);

            foreach ($data as $fieldName => $value) {
                if (isset($record[$fieldName])) {
                    $record[$fieldName] = $value;
                }
            }
            $record->update();

            History::create([
                'model_alias' => 'equipments',
                'record_id' => $record->id,
                'msg' => 'Запись обновлена'
            ]);

        } else {
            $record = Equipments::create($data);

            History::create([
                'model_alias' => 'equipments',
                'record_id' => $record->id,
                'msg' => 'Добавлена новая запись'
            ]);
        }


        //        ObserversController::subscribeByDefaultList([1, 2, 3], $report->id);
//        ObserversController::subscribe($report->user_id, [1, 2, 3], $report->id);
//
//        $msgData = $data;
//        $msgData['report'] = $this->getById($report->id);
//        $message = new Message();
//        $message->setData($msgData)->setTemplateById(2);
//
//        Mailer::setToByEventId(3, $report->id);
//        Mailer::send($message);

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
        $msgData['comment'] = $data;

        $message = new Message();
        $message->setData($msgData)->setTemplateById(6);

        ObserversController::_subscribe(Auth::$user->api_id, 9, $data['record_id']);

        Mailer::setToByEventId([9], $data['record_id']);
        Mailer::send($message);

        return response()->json([
            'data' => $data,
            'meta' => []
        ]);
    }

    function getById($id)
    {
        $record = Equipments::find($id);
        $data = $record->getAttributes();
        $data['user'] = $record->user->getAttributes();
        $data['status'] = $record->status->getAttributes();

        return $data;
    }

    function changeStatus($request)
    {
        $data = $request->all();

        $record = Equipments::find($data['equipment_id']);
        $record->status_id = $data['status_id'];
        $record->status_reason_id = $data['status_reason_id'];
        $record->status_log = $this->setStatusLog($record);

        $record->save();

        return response()->json([
            'data' => $record,
            'meta' => []
        ]);
    }

    function setStatusLog($record)
    {
        $log = (!$record->status_log) ? [] : json_decode($record->status_log);
        $statuses = Statuses::whereIn('model_alias', ['equipments', 'equipments_reason'])
            ->get()
            ->toArray();
        if ($statuses) $statuses = collect($statuses)->keyBy('id');

        array_unshift($log, [
            'status' => $statuses[$record->status_id]['name'],
            'status_reason' => ($record->status_reason_id !== null) ? $statuses[$record->status_reason_id]['name'] : '',
            'datetime' => date("Y-m-d h:i:s"),
            'user_name' => Auth::$user->name,
        ]);

        return json_encode($log);
    }

    function uploadRecordFile(Request $request)
    {
        $data = $request->all();

        if (isset($data['files'])) {

            $files = [];

            foreach ($data['files'] as $item) {
                $File = $item['file'];
                unset($item['file']);
                $files[] = $this->uploadFile($File, $item);
            }

            return response()->json([
                'data' => $files,
                'meta' => []
            ]);

        }
    }

    function uploadFile($file, $fileInfo)
    {
        $file = Uploader::upload($file);

        $file = array_merge($file, $fileInfo);

        File::create($file);

        History::create([
            'model_alias' => 'equipment',
            'record_id' => $file['record_id'],
            'msg' => 'Загружен файл: ' . $file['basename']
        ]);

        return $file;
    }

    function addEngineHours(Request $request)
    {
        $data = $request->all();

        $record = Equipments::find($data['equipment_id']);
        $record->engine_hours = $this->setEngineHoursLog($record, $data);

        $record->save();

        return response()->json([
            'data' => $record,
            'meta' => []
        ]);
    }

    function setEngineHoursLog($record, $data)
    {
        $log = (!$record->engine_hours) ? [] : json_decode($record->engine_hours);

        array_unshift($log, [
            'date_at' => $data['date_at'],
            'date_to' => $data['date_to'],
            'count' => $data['count'],
            'created_at' => date("Y-m-d h:i:s"),
            'user_name' => Auth::$user->name,
        ]);

        return json_encode($log);
    }

    function addEquipmentsAccident(Request $request, $id = null)
    {
        $data = $request->all();

        if (isset($data['id']) && $data['id']) {


            $record = EquipmentAccidents::find($data['id']);

            foreach ($data as $fieldName => $value) {
                if (isset($record[$fieldName])) {
                    $record[$fieldName] = $value;
                }
            }

            $diff = array_diff($data, $record->toArray());

            $record->update();

            History::create([
                'model_alias' => 'equipments',
                'record_id' => $record->equipment_id,
                'msg' => 'Запись обновлена' . json_encode($diff)
            ]);

        } else {
            $record = EquipmentAccidents::create($data);

            History::create([
                'model_alias' => 'equipments',
                'record_id' => $record->equipment_id,
                'msg' => 'Добавлена новая запись'
            ]);
        }


        return response()->json([
            'data' => $record,
            'meta' => []
        ]);
    }
}
