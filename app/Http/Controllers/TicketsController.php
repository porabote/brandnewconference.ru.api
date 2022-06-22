<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Tickets;
use App\Models\File;
use Porabote\FullRestApi\Server\ApiTrait;
use Porabote\Uploader\Uploader;
use App\Http\Controllers\AcceptListsController as AcceptLists;
use App\Http\Components\Mailer\Mailer;
use App\Http\Components\Mailer\Message;

class TicketsController extends Controller
{
    use ApiTrait;

    static $authAllows;

    function __construct()
    {
        self::$authAllows = [
            'create',
        ];
    }

    function create(Request $request)
    {
        $data = $request->all();

        if (!isset($data['id'])) {
            $record = Tickets::create($data);
            AcceptLists::_addStepsByDefault(4, $record->id, 'Tickets');

        } else {
            $record = Tickets::find($data['id']);
            $record->update();
        }

        return response()->json([
            'data' => $record,
            'meta' => []
        ]);
    }

    /*
     * ACCEPT LIST
     * */
    function getAcceptListMode($request)
    {
        $data = $request->all();
        $record = Tickets::find($data['foreignKey']);

        $mode = ($record->status_id == 71)  ? 'building' : 'signing';

        return response()->json([
            'data' => ['mode' => $mode],
            'meta' => []
        ]);
    }
    
    function acceptListEventsCallback($request)
    {
        $data = $request->all();

        switch ($data['action']) {
            case 'setAcceptors': $this->setAcceptorsCallback($data['foreignKey']);
            case 'acceptStep': $this->setAcceptorsCallback($data['foreignKey']);
            case 'declineStep': $this->declineStepCallback($data['foreignKey']);
        }

        return response()->json([
            'data' => $data,
            'meta' => []
        ]);
    }

    function setAcceptorsCallback($id)
    {
        $record = Tickets::with('steps')->find($id);

        $isAllAccepted = true;
        foreach($record['steps'] as $step) {
            if (!$step['acceptor']['accepted_at']) {
                $isAllAccepted = false;
                break;
            }
        }

        if ($isAllAccepted) {
            $record->status_id = 71;
            $record->update();
            return $this->notifyNextSigner($id);
        } else {
            $record->status_id = 72;
            $record->update();
            return $this->notifyAboutAccepting($id);
        }
    }

    function declineStepCallback($id)
    {
        $record = Tickets::find($id);
        $record->status_id = 36;
        $record->update();

        $this->notifyAboutDeclining($id);
    }
    
    function notifyNextSigner($id)
    {
        $data = Tickets::with('steps.acceptor.api_user')
            ->find($id)
            ->toArray();
        $data['record'] = $data;
        $nextSignerEmail = null;

        foreach ($data['steps'] as $step) {
            if (!$step['acceptor']['accepted_at']) {
                $nextSignerEmail = $step['acceptor']['api_user']['email'];
                break;
            }
        }

        if(!$nextSignerEmail) return;

        $message = new Message();
        $message->setData($data)->setTemplateById(12);

        Mailer::setTo([
            [$nextSignerEmail]
        ]);
        Mailer::send($message);
    }

    function notifyAboutAccepting($id)
    {
        $data = Tickets::with('steps.acceptor.api_user')
            ->with('user')
            ->find($id)
            ->toArray();
        $data['record'] = $data;

        $nextSigner = null;
        foreach ($data['steps'] as $step) {
            if (!$step['acceptor']['accepted_at']) {
                $nextSigner = $step['acceptor']['api_user'];
                break;
            }
        }

        if(!$nextSigner) return;
        $data['nextSigner'] = $nextSigner;

        $message = new Message();
        $message
            ->setData($data)
            ->setTemplateById(20);

        Mailer::setTo([
            ['maksimov_den@mail.ru'],
            [$nextSigner['email']]
        ]);
        Mailer::send($message);
    }

    function notifyAboutDeclining($id)
    {
        $data = Tickets::with('steps.acceptor.api_user')
            ->with('user')
            ->find($id)
            ->toArray();
        $data['record'] = $data;

        $nextSigner = null;
        foreach ($data['steps'] as $step) {
            if (!$step['acceptor']['accepted_at']) {
                $nextSigner = $step['acceptor']['api_user'];
                break;
            }
        }

        if(!$nextSigner) return;
        $data['nextSigner'] = $nextSigner;

        $message = new Message();
        $message
            ->setData($data)
            ->setTemplateById(19);

        Mailer::setTo([
            ['maksimov_den@mail.ru'],
            [$nextSigner['email']]
        ]);
        Mailer::send($message);
    }

}