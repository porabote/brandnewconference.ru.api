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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class EquipmentsController extends Controller
{
    use ApiTrait;

    static $authAllows;
    private $authData = [];

    function __construct()
    {
        self::$authAllows = [
            'exportToExcel',
            'exportFeedToExcel'
        ];
    }

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
            $dataBefore = $record->getAttributes();

            foreach ($data as $fieldName => $value) {
                if (isset($record[$fieldName])) {
                    $record[$fieldName] = $value;
                }
            }

            $record->update();

            History::create([
                'model_alias' => 'equipments',
                'record_id' => $record->equipment_id,
                'msg' => 'Изменена запись - Авария ID ' . $record->id,
                'diff' => History::setDiff($dataBefore, $data),
            ]);

        } else {
            $record = EquipmentAccidents::create($data);

            History::create([
                'model_alias' => 'equipments',
                'record_id' => $record->equipment_id,
                'msg' => 'Добавлена новая запись Авария ID ' . $record->id,
            ]);
        }


        return response()->json([
            'data' => $record,
            'meta' => []
        ]);
    }

    function markToDeleteHours($request)
    {
        $data = $request->all();

        $record = Equipments::find($data['record_id']);
        $repairs = json_decode($record->engine_hours, true);

        $deletedPart = $repairs[$data['key']];
        unset($repairs[$data['key']]);
        $record->engine_hours = json_encode(array_values($repairs));
        $record->update();

        History::create([
            'model_alias' => 'equipments',
            'record_id' => $data['record_id'],
            'msg' => 'Удалена наработка ' . $deletedPart['count'],
        ]);

        return response()->json([
            'data' => $record,
            'meta' => []
        ]);
    }

    function exportToExcel($response)
    {
        $alphabet = range('A', 'Z');

        $data = Equipments::with('equipment_repairs')
            ->with('equipment_repairs.spares')
            ->with('equipment_repairs.spares.spare')
            ->with('equipment_repairs.user')
            ->with('equipment_accidents')
            ->with('organizations_own')
            ->with('object')
            ->with('object.platform')
            ->with('type')
            ->with('status')
            ->with('status_reason')
            ->find($response->query('id'))->toArray();

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(storage_path() . '/export/equipments/equipment_rep.xlsx');

        $styleArray = [
//            'font' => [
//                'bold' => true,
//            ],
            'alignment' => [
                'wrapText' => true,
            //    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('argb' => '00000000'),
                ),
            ),
        ];

        $styleBold = [
            'font' => [
                'bold' => true,
            ],
        ];

        // Общие данные
        $sheet = $spreadsheet->getSheet(0);
        $sheet->setCellValue('B2', $data['organizations_own']['name']);
        $sheet->setCellValue('B3', $data['object']['platform']['ru_alias']);
        $sheet->setCellValue('B4', $data['object']['name']);
        $sheet->setCellValue('B5', $data['sap_number']);
        $sheet->setCellValue('B6', $data['type']['name']);
        $sheet->setCellValue('B7', $data['name']);
        $sheet->setCellValue('B8', $data['brand_name']);
        $sheet->setCellValue('B9', $data['factory_name']);
        $sheet->setCellValue('B10', $data['vin_code']);
        $sheet->setCellValue('B11', $data['inventory_number']);
        $sheet->setCellValue('B12', date('d-m-Y', strtotime($data['release_date'])));
        $sheet->setCellValue('B13', date('d-m-Y', strtotime($data['operation_start'])));
        $sheet->setCellValue('B14', $data['status']['name']);
        $sheet->setCellValue('B15', $data['status_reason']['name']);

        // Часы
        $sheet = $spreadsheet->getSheet(1);
        $row = 3;
        $hoursData = $this->setHours(json_decode($data['engine_hours'], true));
        foreach ($hoursData['hours'] as $year => $months) {
            $sheet->setCellValue('A' . $row, $year);
            foreach ($months as $month => $value) {
                $sheet->setCellValue($alphabet[intval($month)] . $row, $value);
            }
            $sheet->getStyle('A' . $row . ':M' . $row)->applyFromArray($styleArray);
            $row++;
        }
        $sheet->setCellValue('A' . $row, 'Итого');
        $sheet->setCellValue('B' . $row, $hoursData['amount']);
        $sheet->getStyle('A' . $row . ':M' . $row)->applyFromArray($styleArray);
        $sheet->getStyle('A' . $row . ':M' . $row)->applyFromArray($styleBold);

        // debug($data['equipment_repairs']);exit();
        // ТО, ремонт
        $sheet = $spreadsheet->getSheet(2);
        $row = 7;
        $types = ['' => 'Не указано', 'to' => 'Технический осмотр', 'repair' => 'Ремонт'];

        foreach ($data['equipment_repairs'] as $repair) {

            $downtimeDays = $repair['downtime'] / 24;
            $dateStart = new \DateTime($repair['date_at']);
            $dateEnd = $dateStart->modify('+' . ceil($downtimeDays) . ' days');

            $sheet->setCellValue('A' . $row, $types[$repair['type']]);
            $sheet->setCellValue('B' . $row, $repair['engine_hours']);
            $sheet->setCellValue('C' . $row, date('d-m-Y', strtotime($repair['date_at'])));
            $sheet->setCellValue('D' . $row, $dateEnd->format('d-m-Y'));
            $sheet->setCellValue('E' . $row, $repair['downtime']);
            $sheet->setCellValue('F' . $row, $repair['desc_short']);
            $sheet->setCellValue('J' . $row, $repair['user']['name'] . '( ' . $repair['user']['post_name'] . ' )');
//debug($data);exit();
            foreach ($repair['spares'] as $node) {
                $sheet->setCellValue('G' . $row, $node['spare']['name']);
                $sheet->setCellValue('H' . $row, $node['spare']['vendor_code']);
                $sheet->setCellValue('I' . $row, $node['count']);
                $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray($styleArray);
                $row++;
            }
            $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray($styleArray);
            $row++;
        }

        // Аварии
        $sheet = $spreadsheet->getSheet(3);
        $row = 4;
        foreach ($data['equipment_accidents'] as $accident) {
            $sheet->setCellValue('A' . $row, date('d-m-Y', strtotime($accident['date'])));
            $sheet->setCellValue('B' . $row, $accident['act_number']);
            $sheet->setCellValue('C' . $row, $accident['details']);
            $sheet->setCellValue('D' . $row, $accident['reasons']);
            $sheet->setCellValue('E' . $row, $accident['downtime']);
            $sheet->setCellValue('F' . $row, $accident['measures']);
            $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray($styleArray);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'equipments_N' . $data['id'] . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        $writer->save('php://output');
    }

    function setHours($hours)
    {
        $years = [];
        $dataSorted = [];
        $amount = 0;

        if (!$hours) {
            return [
                'hours' => [],
                'amount' => $amount,
                'years' => $years
            ];
        };

        foreach ($hours  as $hour) {

            $date = explode('-', $hour['date_at']);
            $years[$date[0]] = $date[0];

            $alias = $date[0] . '-' . $date[1];
            if (!isset($dataSorted[$date[0]][$date[1]])) {
                $dataSorted[$date[0]][$date[1]] = $hour['count'];
            } else {
                $dataSorted[$date[0]][$date[1]] += $hour['count'];
            }
            $amount += $hour['count'];
        }
        return [
            'hours' => $dataSorted,
            'amount' => $amount,
            'years' => $years
        ];
    }

    function exportFeedToExcel($request)
    {
        $data = $request->all();

        $ids = explode('|', $data['ids']);

        $records = Equipments::whereIn('id', $ids)
            ->with('organizations_own')
            ->with('object')
            ->with('object.platform')
           // ->with('type')
            ->with('status')
           // ->with('status_reason')
            ->get()
            ->toArray();

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(storage_path() . '/export/equipments/equipments_list.xlsx');
        $styleArray = [
            'alignment' => [
                'wrapText' => true,
                //    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('argb' => '00000000'),
                ),
            ),
        ];
        $sheet = $spreadsheet->getSheet(0);
        $row = 5;
        foreach ($records as $record) {
            //debug($record);exit();
            //$sheet->setCellValue('A' . $row, date('d-m-Y', strtotime($accident['date'])));
            $sheet->setCellValue('B' . $row, $record['id']);
            $sheet->setCellValue('C' . $row, $record['name']);
            $sheet->setCellValue('D' . $row, $record['inventory_number']);
            $sheet->setCellValue('E' . $row, $record['status']['name']);
            $sheet->setCellValue('F' . $row, $record['organizations_own']['name']);
            $sheet->setCellValue('G' . $row,
                isset($record['object']['platform']) ? $record['object']['platform']['ru_alias'] : '');
            $sheet->setCellValue('H' . $row, $record['object']['name']);

            $sheet->getStyle('B' . $row . ':H' . $row)->applyFromArray($styleArray);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'equipments_list.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        $writer->save('php://output');

    }
}
