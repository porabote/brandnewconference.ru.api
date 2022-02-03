<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Porabote\FullRestApi\Server\ApiTrait;
use App\Models\PaymentsSets;
use App\Models\Payments;
use App\Models\Files;
use App\Models\History;
use App\Models\Comment;
use App\Models\Configs;
use App\Models\Dicts;
use App\Http\Components\Mailer\Mailer;
use App\Http\Components\Mailer\Message;
use App\Http\Controllers\ObserversController;
use App\Http\Components\Thyssen\Schneider\Schneider;
use Porabote\Auth\Auth;
use Porabote\Components\ImageMagick\ImageMagickHandler;

class PaymentsController extends Controller
{

    // Company client guid => shneider folder alias
    private $system_aliases = [
        '000e40765a8-7e39-11dc-bd9a-000255dfb035' => 'dev',
        'e40765a8-7e39-11dc-bd9a-000255dfb035' => 'Norilsk',
        '786f130b-9be7-11db-8f46-0017314d44cc' => 'TMCE',
        '0545b3a8-1529-11e2-8dab-0050568f0010' => 'Solikamsk'
    ];

    function requestForCancelPayment($request, $id)
    {
        $data = $this->getPaymentData($id);
        
        if (!$data['accept_datetime']) {
            
            Schneider::connect();

            //$this->system_aliases[$data['client']['guid_schneider']] = 'dev';
            $filesList = Schneider::readFolder('/Thyssen24/' .$this->system_aliases[$data['client']['guid_schneider']]. '/xml_in/');
            
            foreach ($filesList as $fileName) {

                preg_match('/(payment_' . $id . ')/', $fileName, $matches);
                if ($matches) {

                    $content = Schneider::read($fileName);
                    Schneider::putToRemote(
                        str_replace('xml_in', 'xml_cancel', $fileName),
                        $content
                    );
                    $content = Schneider::deleteFile($fileName);
                    break;
                }
            }
            
            Schneider::disconnect();
        }

        // Меняем статус
        $payment = Payments::find($id);
        $payment->status_id = 55;
        $payment->save();

        // Отправляем письм
        $data['comment'] = $request->input('comment');
        $data['sender']['fio'] = Auth::$user->name;

        $message = new Message();
        $message
            ->setData($data)
            ->setTemplateById(5);

        Mailer::setToByDefault([7]);
        Mailer::send($message);

        //Пишем хистори
        History::create([
            'model_alias' => 'payments-sets',
            'record_id' => $data['payments_set_id'],
            'msg' => 'Платеж N ' . $data['id'] . ' повторно отправлен на оплату. Комментарий: ' . $data['comment']
        ]);

        return response()->json([
            'data' => $data,
            'meta' => []
        ]);
    }
    
    function cancelRequestApprove($request, $id)
    {
        // Меняем статус
        $payment = Payments::find($id);
        $payment->update(['status_id' => 58]);

        //Пишем хистори
        History::create([
            'model_alias' => 'payments',
            'record_id' => $id,
            'msg' => 'Платеж N ' . $id . ' был отменён после заявки на отмену. Платёж не был проведен. Комментарий: ' . $request->input('comment')
        ]);

        return response()->json([
            'data' => [],
            'meta' => []
        ]);
    }

    function cancelRequestDecline($request, $id)
    {
        // Меняем статус
        $payment = Payments::find($id);
        $payment->update(['status_id' => 56]);

        //Пишем хистори
        History::create([
            'model_alias' => 'payments',
            'record_id' => $id,
            'msg' => 'Платеж N ' . $id . ' не был отменён после заявки на отмену. Платёж отплачен. 
                Комментарий: ' . $request->input('comment')
        ]);
        
        return response()->json([
            'data' => [],
            'meta' => []
        ]);
    }
    
    function repeatPayment($request, $id)
    {
        $this->sendForPayment($id);

        // Отправляем письмо
        $data = $this->getPaymentData($id);
        $data['comment'] = $request->input('comment');
        $data['sender']['fio'] = Auth::$user->name;

        $message = new Message();
        $message
            ->setData($data)
            ->setTemplateById(4);

        Mailer::setToByDefault([6]);
        Mailer::send($message);

        //Пишем хистори
        History::create([
            'model_alias' => 'payments-sets',
            'record_id' => $data['payments_set_id'],
            'msg' => 'Платеж N ' . $data['id'] . ' повторно отправлен на оплату. Комментарий: ' . $data['comment']
        ]);

        return response()->json([
            'data' => $data,
            'meta' => []
        ]);
    }

    function getPaymentData($id)
    {
        return Payments::with([
            'contractor',
            'client',
            'object',
            'bill'
        ])
            ->find($id)
            ->toArray();
    }

    function sendForPayment($id)
    {
        $paymentObj = Payments::with([
            'contractor',
            'client',
            'object',
            'bill'
        ])->find($id);

        $payment = $paymentObj->toArray();

        $pdfPath = $this->putScansToPdf($id, 'scan_for_scheider');

        $xml = $this->setPaymentXmlSchema($payment);
        //$this->system_aliases[$payment['client']['guid_schneider']] = 'dev';

        $fileName = '/Thyssen24/' .$this->system_aliases[$payment['client']['guid_schneider']]. '/xml_in/payment_' . $id . '__' . time();
        $pdfName = '/Thyssen24/' .$this->system_aliases[$payment['client']['guid_schneider']]. '/pdf_in/payment_' . $id . '__' . time();

        Schneider::connect();
        $success = Schneider::putToRemote($fileName . '.xml', $xml);

        if($pdfPath) {
            $success = Schneider::putToRemote($pdfName . '.pdf', file_get_contents($pdfPath), 'FTP_BINARY');
        }

        Schneider::disconnect();

        if($success) {
            $paymentObj->status_id = 57;
            $paymentObj->save($payment);
        }

    }

    public function putScansToPdf($id, $outputName = null)
    {

        $scansShareList = [];

        $lists = $this->getScanFiles($id, false);

        ImageMagickHandler::$tmpPath = storage_path() . '/ImageMagick/tmp';

        foreach($lists as $list) {

            $newImagePath = ImageMagickHandler::cloneToTmp($list['path']);
            foreach ($list['child'] as $elementOfImage) {

                $x = 0;
                $y = 0;

                if($elementOfImage['data_json']) {

                    $axis = json_decode($elementOfImage['data_json'], true);

                    $x = $axis['axis']['top'];
                    $y = $axis['axis']['left'];
                }

                ImageMagickHandler::composite($newImagePath, $elementOfImage['path'], $x, $y);

            }

            $scansShareList[] = $newImagePath;

        }
        ob_clean();

        $Mpdf = new \Mpdf\Mpdf;
        foreach ($scansShareList as $list)
        {
            $Mpdf->WriteHTML('<img src="' . $list . '">');
        }

        if(!$outputName) {
            $Mpdf->Output($id . 'payments__scans.pdf', \Mpdf\Output\Destination::INLINE);
            exit();
        } else {
            if($scansShareList) {
                $path = ImageMagickHandler::$tmpPath . '/' . time() . '__' . $outputName . '.pdf';
                $Mpdf->Output($path, \Mpdf\Output\Destination::FILE);
                return $path;
            } else {
                return null;
            }
        }

    }

    /*
 *  Get/Set Scans
 *
 * */
    function getScanFiles($id)
    {

        $payment = Payments::find($id);

        $paymentScans = [];

        if(!$payment->bill_file_id) {
            //$this->refreshScanFiles($payment->id);
        } else {
            // Если есть сканы, находим их
            $paymentScans = Files::limit(100)
                ->where('record_id', $payment->id)
                ->where('model_alias', 'App.Payments')
                ->where('label', 'imgFromPdf')
                ->where('flag', 'on')
                ->where('parent_id', $payment->bill_file_id)
                ->get()
                ->toArray();

            //Печати и подписи
            foreach($paymentScans as &$scan) {
                $scan['child'] =
                    Files::where('parent_id', $scan['id'])
                        ->whereIn('label', ['paymentSighTable', 'signInTable'])
                        ->where('flag', 'on')
                        ->get()
                        ->toArray();
            }

            return $paymentScans;
        }
    }

    /**
     * Opens the current file with a given $mode
     *
     * @param string $paymentId integer - payment ID
     * @return void
     */
    public function setPaymentXmlSchema($payment)
    {
        $payments_types = \App\Models\PaymentsTypes::get()->pluck('name', 'value')->toArray();

        Schneider::connect();
        $simpleXml = Schneider::readFile('/Thyssen24/patterns/payment.xml');

        $simpleXml->Payment->DatePayment = date('d.m.Y', strtotime($payment['date_payment']));
        $simpleXml->Payment->PaymentGUID = $payment['guid'];
        $simpleXml->Payment->PaymentThyssenId = $payment['id'];

        // Плательщик (Тиссен)
        $simpleXml->Payment->Contractor->FullName = $payment['contractor']['name'];
        $simpleXml->Payment->Contractor->INN = $payment['contractor']['inn'];
        $simpleXml->Payment->Contractor->KPP = $payment['contractor']['kpp'];
        $simpleXml->Payment->Contractor->GUID = $payment['contractor']['guid_schneider'];

        // Получатель платежа
        $simpleXml->Payment->Client->FullName = $payment['client']['name'];
        $simpleXml->Payment->Client->INN = $payment['client']['inn'];
        $simpleXml->Payment->Client->KPP = $payment['client']['kpp'];
        $simpleXml->Payment->Client->Project = str_replace('АБК Талнах', 'СКС-1', $payment['object']['name']);
        $simpleXml->Payment->Client->GUID = $payment['client']['guid_schneider'];

        // Счёт (документ)
        $simpleXml->Payment->Bill->Number = $payment['bill']['number'];
        $simpleXml->Payment->Bill->Date = date('d.m.Y h:i:s', strtotime($payment['bill']['date']));

        // Данные платежа
        $simpleXml->Payment->Bill->CurrencyBill = str_replace('RUR', 'руб.', $payment['bill']['currency']);
        $simpleXml->Payment->Bill->CurrencyPayment = 'руб.';

        $summa = $payment['summa'];
        $nds_summa = $payment['nds_summa'];
        if($payment['bill']['currency'] == 'EUR') {
            $summa = $payment['summa'] * $payment['payments_set']['rate_euro'];
            $nds_summa = $payment['nds_summa'] * $payment['payments_set']['rate_euro'];
        } else if ($payment['bill']['currency'] == 'USD') {
            $summa = $payment['summa'] * $payment['payments_set']['rate_usd'];
            $nds_summa = $payment['nds_summa'] * $payment['payments_set']['rate_usd'];
        }

        $simpleXml->Payment->Summa = number_format(round($summa, 2), 2, '.', '');
        $simpleXml->Payment->NdsPercent = ($payment['nds_percent']) ? $payment['nds_percent'] : 'Без НДС';
        $simpleXml->Payment->NdsSumma = number_format(round($nds_summa, 2), 2, '.', '');

        $simpleXml->Payment->PurposeOfPayment = mb_strtolower($payment['purpose']);
        $simpleXml->Payment->PercentOfPayment = $payment['percent_of_bill'];

        $simpleXml->Payment->VoCode = '((VO' .$payment['vo_code']. '))';
        $simpleXml->Payment->VoType = mb_strtolower($payments_types[$payment['pay_type']]);

        Schneider::disconnect();

        return $simpleXml->asXML();

    }

}