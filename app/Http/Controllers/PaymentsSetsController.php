<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Porabote\FullRestApi\Server\ApiTrait;
use App\Models\PaymentsSets;
use App\Models\Payments;
use App\Models\History;
use App\Models\Comment;
use App\Http\Components\Mailer\Mailer;
use App\Http\Components\Mailer\Message;
use App\Http\Controllers\ObserversController;
use App\Http\Components\Thyssen\Schneider\Schneider;
use Porabote\Auth\Auth;

class PaymentsSetsController extends Controller
{
    use ApiTrait;

    //https://api.thyssen24.ru/api/payments-sets/method/getPaymentsFeedbacks
    function getPaymentsFeedbacks()
    {
        Schneider::connect();

        $alias = str_replace('Thyssen', 'Norilsk', Auth::$user->account_alias);

        $filesList = Schneider::readFolder('/Thyssen24/' . $alias . '/xml_in_loaded');

        $paths = [
            'payment' => [],
            'contractor' => []
        ];
        foreach ($filesList as $filePath)
        {
            $filePrefix = explode('_', pathinfo($filePath)['filename'])[0];
            $paths[$filePrefix][] = $filePath;
        }
        $handledPayments = $this->handlePaymentsList($paths['payment']);

        Schneider::disconnect();

        return response()->json([
            'data' => $handledPayments,
            'meta' => []
        ]);
    }

    function handlePaymentsList($list)
    {
        $i = 0;

        $handledPayments = [];

        foreach ($list as $path) {

            if ($i > 300) break;

            $paymentXml = Schneider::readFile($path);

            $id = (string) $paymentXml->Payment->PaymentThyssenId;
            $guid = (string) $paymentXml->Payment->PaymentGUID;
            $accept_datetime = (string) $paymentXml->Payment->Ftime;

            $handledPayments[$id] = [
                'id' => $id,
                'guid' => $guid,
                'accept_datetime' => $accept_datetime
            ];

            $payment = Payments::find($id);

            if ($payment) {

                $payment->guid_schneider = $guid;
                $payment->accept_datetime = date('Y-m-d H:i:s', strtotime($accept_datetime));
                $payment->status_id = 56;
                $payment->save();

                Schneider::deleteFile($path);
            }

            $i++;
        }

        return $handledPayments;
    }

    function addComment(Request $request)
    {
        $data = $request->all();

        Comment::create($data);

        $msgData = $data;
        $msgData['payments-set'] = $this->getById($data['record_id']);

        $message = new Message();
        $message->setData($msgData)->setTemplateById(1);

        Mailer::setToByEventId([1], $data['record_id']);
        Mailer::send($message);

        return response()->json([
            'data' => $data,
            'meta' => []
        ]);
    }

    function getById($id)
    {
        $set = PaymentsSets::find($id);
        $data = $set->getAttributes();
        $data['bill'] = $set->bill->getAttributes();
        return $data;
    }

}
