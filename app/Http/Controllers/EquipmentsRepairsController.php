<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EquipmentsRepairs;
use App\Models\History;
use Porabote\FullRestApi\Server\ApiTrait;

class EquipmentsRepairsController extends Controller
{
    use ApiTrait;
    
    function add($request)
    {
        $data = $request->all();

        if (isset($data['id']) && $data['id']) {


            $record = EquipmentsRepairs::find($data['id']);

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
                'msg' => 'Изменена запись - Ремонт ' . json_encode($diff)
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
}
