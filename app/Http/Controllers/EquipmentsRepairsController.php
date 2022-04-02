<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EquipmentsRepairs;
use App\Models\EquipmentsRepairsSpares;
use App\Models\History;
use Porabote\FullRestApi\Server\ApiTrait;
use App\Http\Controllers\SparesController;

class EquipmentsRepairsController extends Controller
{
    use ApiTrait;
    
    function add($request)
    {
        $data = $request->all();

        if (isset($data['id']) && $data['id']) {

            $record = EquipmentsRepairs::find($data['id']);
            $dataBefore = $record->getAttributes();

            foreach ($data as $fieldName => $value) {
                    $record->$fieldName = $value;
            }

            $record->update();

            History::create([
                'model_alias' => 'equipments',
                'record_id' => $record->equipment_id,
                'msg' => 'Изменена запись - ТО и ремонт ID ' . $record->id,
                'diff' => History::setDiff($dataBefore, $data),
            ]);

        } else {
            $record = EquipmentsRepairs::create($data);

            History::create([
                'model_alias' => 'equipments',
                'record_id' => $record->equipment_id,
                'msg' => 'Добавлена новая запись - Ремонт'
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

    function backToStore($request, $nodeId)
    {
        $record = EquipmentsRepairsSpares::find($nodeId);
        //debug($record->toArray());
        SparesController::setRemainLog($record['spare_id'], $record['count'], 0, $record['repair_id']);
        $record->delete();

        return response()->json([
            'data' => $record->toArray(),
            'meta' => []
        ]);
    }
}
