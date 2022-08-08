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

class ConsumersController extends Controller
{
    use ApiTrait;

    static $authAllows;
    private $authData = [];

    function __construct()
    {
        self::$authAllows = [
            'importHashes',
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