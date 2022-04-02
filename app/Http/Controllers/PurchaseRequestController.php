<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseRequest;
use App\Models\File;
use Porabote\FullRestApi\Server\ApiTrait;
use Porabote\Uploader\Uploader;
use App\Models\History;
use App\Models\Comment;
use App\Models\Config;
use App\Http\Components\Mailer\Mailer;
use App\Http\Components\Mailer\Message;
use App\Http\Controllers\ObserversController;
use Porabote\Auth\Auth;

class PurchaseRequestController extends Controller
{
    use ApiTrait;

    function addComment(Request $request)
    {
        $data = $request->all();

        Comment::create($data);

        $msgData = $data;
        $msgData['record'] = $this->getById($data['record_id']);
        $msgData['comment'] = $data;

        $message = new Message();
        $message->setData($msgData)->setTemplateById(7);

        ObserversController::_subscribe([Auth::$user->api_id], [10], $data['record_id']);

        Mailer::setToByEventId([10], $data['record_id']);
        Mailer::send($message);

        return response()->json([
            'data' => $data,
            'meta' => []
        ]);
    }

    function getById($id)
    {
        $record = PurchaseRequest::find($id);
        $data = $record->getAttributes();
        $data['user'] = $record->user->getAttributes();
        $data['status'] = $record->status->getAttributes();
        $data['initator'] = $record->initator->getAttributes();
        $data['object'] = $record->object->getAttributes();

        return $data;
    }

}
