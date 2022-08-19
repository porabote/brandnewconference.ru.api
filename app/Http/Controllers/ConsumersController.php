<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Consumers;
use Porabote\FullRestApi\Server\ApiTrait;
use Porabote\Uploader\Uploader;
use App\Http\Components\Mailer\Mailer;
use App\Http\Components\Mailer\Message;
use App\Models\Hashes;
use Porabote\Components\Excel\Excel;

class ConsumersController extends Controller
{
    use ApiTrait;

    static $authAllows;
    private $authData = [];

    function __construct()
    {
        self::$authAllows = [
            'importHashes',
            'exportToExcel'
        ];
    }

    function acceptPart($request)
    {
        $data = $request->all();

        $consumer = Consumers::find($data['id']);
        $consumer->status = 'accepted';
        $consumer->update();


        $message = new Message();

        $message->setData([])->setTemplateById(31);
        Mailer::setTo($consumer->email);
        Mailer::setTo('maksimov_den@mail.ru');
       // Mailer::setTo('valeria.dunets@dentsu.ru');
       // Mailer::setTo('alexandra.sedinkina@dentsu.ru');
       // Mailer::setTo('Anastas.Sarkisyan@dentsu.ru');

        Mailer::send($message);
        
        return response()->json([
            'data' => $consumer->toArray(),
            'meta' => []
        ]);
    }
    
    function declinePart($request)
    {
        $data = $request->all();

        $consumer = Consumers::find($data['id']);
        $consumer->status = 'declined';
        $consumer->part_type = 'online';
        $consumer->update();

        $message = new Message();
        $message->setData([])->setTemplateById(30);
        Mailer::setTo($consumer->email);
      //  Mailer::setTo('alexandra.sedinkina@dentsu.ru');
        Mailer::send($message);

        return response()->json([
            'data' => $consumer->toArray(),
            'meta' => []
        ]);
    }

    function importHashes()
    {
        $path = '/var/www/porabote/data/www/brandnewconference.ru.api/storage/import/members.xlsx';

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $countRow = $sheet->getHighestRow();

        for ($i = 1; $i <= $countRow; $i++) {
            Hashes::create([
                'hash' => $sheet->getCell('A' . $i)->getValue(),
                'part_format' => $sheet->getCell('B' . $i)->getValue(),
            ]);
        }
    }

    function exportToExcel()
    {
        $excel = new Excel(config('paths.base_path') . '/storage/export/consumers/consumers_export.xlsx');
        $list = $excel->getActiveSheet();

        $consumers = Consumers::get()->toArray();

        $row = 2;
        foreach ($consumers as $consumer) {
            $list->setCellValue('A' . $row, "{$consumer['last_name']}");
            $list->setCellValue('B' . $row, "{$consumer['name']}");
            $list->setCellValue('C' . $row, "{$consumer['post_name']}");
            $list->setCellValue('D' . $row, "{$consumer['company_name']}");
            $list->setCellValue('E' . $row, "{$consumer['email']}");
            $list->setCellValue('F' . $row, "{$consumer['phone']}");
            $list->setCellValue('G' . $row, "{$consumer['part_type']}");
            $list->setCellValue('H' . $row, "{$consumer['user_id']}");
            $list->setCellValue('I' . $row, "{$consumer['status']}");
            $list->setCellValue('J' . $row, "{$consumer['created_at']}");
            $row++;
        }

        $excel->output('php://output', 'consumers_export');
    }
//    function create(Request $request)
//    {
//        $data = $request->all();
//
//        if (!isset($data['id'])) {
//            $record = Faq::create($data);
//
//        } else {
//            $record = Faq::find($data['id']);
//            $record->update();
//        }
//
//        return response()->json([
//            'data' => $record,
//            'meta' => []
//        ]);
//    }
//
//    function edit($request)
//    {
//        $data = $request->all();
//
//        $record = Faq::find($data['id']);
//
//        foreach ($data as $field => $value) {
//            if (array_key_exists($field, $record->getAttributes())) $record->$field = $value;
//        }
//
//        $record->update();
//
//        return response()->json([
//            'data' => $record,
//            'meta' => []
//        ]);
//    }
}