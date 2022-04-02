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
use App\Models\SparesRemains;
use App\Models\EquipmentsRepairs;
use App\Models\EquipmentsRepairsSpares;

class SparesController extends Controller
{
    use ApiTrait;

    function add(Request $request)
    {
        $data = $request->all();

        if (!isset($data['id'])) {
            $record = Spares::create($data);

            History::create([
                'model_alias' => 'spares',
                'record_id' => $record->id,
                'msg' => 'Добавлена новая запись'
            ]);

//            SparesRemains::create([
//                'spare_id' => $record->id,
//                'remain' => $record->quantity,
//                'comment' => 'Принято на склад в количестве ' . $record->quantity,
//            ]);
        } else {
            $record = Spares::find($data['id']);
            $dataBefore = $record->getAttributes();

            foreach ($data as $fieldName => $value) {
                if (isset($record[$fieldName])) {
                    $record[$fieldName] = $value;
                }
            }
            $record->update();

            History::create([
                'model_alias' => 'spares',
                'record_id' => $record->id,
                'msg' => 'Данные записи обновлены',
                'diff' => History::setDiff($dataBefore, $data),
            ]);

//            if ($record->status_id == 66) {
//                SparesRemains::create([
//                    'spare_id' => $record->id,
//                    'remain' => $record->quantity,
//                    'comment' => 'Принято на склад в количестве ' . $record->quantity,
//                ]);
//            }
        }
        
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

//        Mailer::setToByEventId([188888], $data['record_id']);
//        Mailer::send($message);

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
        //$data['status'] = $record->status->getAttributes();
        return $data;
    }

    function attachToRepair($request)
    {
        $data = $request->all();

        $node = EquipmentsRepairsSpares::get()
            ->where('repair_id', $data['repair_id'])
            ->where('spare_id', $data['spare_id'])
            ->first();

        if(!$node) {
            $node = EquipmentsRepairsSpares::create($data);
            $spareQuantity = self::setRemainLog($data['spare_id'], 0, $data['count'], $data['repair_id']);
        } else {
            $spareQuantity = self::setRemainLog($data['spare_id'], $node->count, $data['count'], $data['repair_id']);
            $node->count = $data['count'];
            $node->save();
        }

        return response()->json([
            'data' => array_merge($node->toArray(), ['store_remain' => $spareQuantity]),
            'meta' => []
        ]);
    }

    static function setRemainLog($spare_id, $usedBefore, $usedAfter, $repair_id)
    {
        $spare = Spares::find($spare_id);

        $repair = EquipmentsRepairs::find($repair_id);

        $delta = $usedBefore - $usedAfter;

        // If it reduce a store quantity
        if ($delta < 0) {
            $spare->quantity -= abs($delta);

            SparesRemains::create([
                'spare_id' => $spare_id,
                'remain' => $spare->quantity,
                'comment' => 'Списано со склада в количестве ' . abs($delta) .
                    ' в ремонт <a target="_blank" href="/porabote/equipments/view/' . $repair['equipment_id'] . '">ID:' . $repair['equipment_id'] . '</a>',
            ]);
        } else {
            $spare->quantity += $delta;

            SparesRemains::create([
                'spare_id' => $spare_id,
                'remain' => $spare->quantity,
                'comment' => 'Возвращено на склад в количестве ' . abs($delta) .
                    ' в ремонт <a target="_blank" href="/porabote/equipments/view/' . $repair_id . '">ID:' . $repair_id . '</a>',
            ]);
        }

        if ($spare->quantity == 0) {
            $spare->status_id = 68;
        } else {
            $spare->status_id = 67;
        }
        $spare->save();

        return $spare->quantity;
    }

    function detachToRepair($request)
    {
        $data = $request->all();

        $node = EquipmentsRepairsSpares::get()
            ->where('repair_id', $data['repair_id'])
            ->where('spare_id', $data['spare_id'])
            ->first();
        $node->delete();

        return response()->json([
            'data' => $node,
            'meta' => []
        ]);
    }

    function setAcceptStatus($request, $id)
    {
        $spare = Spares::find($id);
        $spare->status_id = 67;
        $spare->save();

        SparesRemains::create([
            'spare_id' => $spare->id,
            'remain' => $spare->quantity,
            'comment' => 'Принято на склад в количестве ' . $spare->quantity,
        ]);

        return response()->json([
            'data' => $spare->toArray(),
            'meta' => []
        ]);
    }

    function moveToOtherStore($request, $id)
    {
        $data = $request->all();
        $spare = Spares::find($id);

        $data['quantity'] = $data['quantity_moved'];
        $newEntity = Spares::create($data);
        History::create([
            'model_alias' => 'spares',
            'record_id' => $newEntity->id,
            'msg' => 'Перемещено из остатков записи <a class="comments_link" target="_blank" href="/porabote/spares/view/' . $spare->id . '">' . $spare->id . '</a>'
        ]);

        $spare->quantity -= $data['quantity_moved'];
        $spare->save();
        History::create([
            'model_alias' => 'spares',
            'record_id' => $spare->id,
            'msg' => 'Часть остатков перемещена в запись <a class="comments_link" target="_blank" href="/porabote/spares/view/' . $newEntity->id . '">' . $newEntity->id . '</a>'
        ]);

        return response()->json([
            'data' => $newEntity->toArray(),
            'meta' => []
        ]);
    }
}