<?php

namespace App\Http\Controllers;

use App\Http\Components\Mailer\Mailer;
use App\Http\Components\Mailer\Message;
use Porabote\Auth\Auth;
use Porabote\FullRestApi\Server\ApiTrait;
use App\Models\Speakers;
use App\Models\Hashes;
use App\Models\Faq;
use App\Models\Consumers;
use App\Models\Feedbacks;
use App\Models\TextBoxes;
use App\Models\Partners;
use App\Models\Timings;
use App\Models\Questionnaires;
use App\Models\QuestionnairesVariants;
use App\Models\QuestionnairesVariantsLogs;
use App\Exceptions\ApiException;

class LandingController extends Controller
{
    use ApiTrait;

    static $authAllows;
    private $authData = [];

    function __construct()
    {
        self::$authAllows = [
            'get',
            'registration',
            'createQuestion',
            'vote',
        ];
    }

    function get($request)
    {

        $data = $request->all();

        $hash = null;
        if ($request->input('userId')) {
            $hash = Hashes::where('hash', $request->input('userId'))->get()->first();
            if($hash) {
                $hash = $hash->toArray();
            }
        }

        $speakers = Speakers::orderBy('lft')->where('active_flg', 1)->with('avatar')->get();
        $faqs = Faq::orderBy('lft')->get();
        $textBoxes = TextBoxes::get();
        $partners = Partners::orderBy('lft')->where('active_flg', 1)->with('avatar')->get();
        $timings = Timings::with('topics.speakers')->get();
        $questionnaires = Questionnaires::with('variants')->get();

        return response()->json([
            'data' => [
                'speakers' => $speakers,
                'faqs' => $faqs,
                'hash' => $hash,
                'textBoxes' => $textBoxes,
                'partners' => $partners,
                'timings' => $timings,
                'questionnaires' => $questionnaires,
            ],
            'meta' => []
        ]);
    }

    function registration($request)
    {
        try {
            $data = $request->all();

            if (!$request->input('accept')) {
                throw new ApiException('Пожалуйста, укажите согласие на обработку персональных данных.');
            }

            foreach (Consumers::$requiredFields as $requiredField) {
                if (!isset($data[$requiredField])) {
                    throw new ApiException('Пожалуйста, заполните все обязательные поля.');
                }
            }

            if (!filter_var($request->input('email'), FILTER_VALIDATE_EMAIL)) {
                throw new ApiException('Пожалуйста, проверьте формат электронного адреса.');
            }

            if (!empty($request->input('user_id'))) {
                $hashRecord = Hashes::where('hash', $request->input('user_id'))->get()->first();
                $hashRecord->hash = '';
                $hashRecord->update();
            }
            
            $consumer = Consumers::where('email', $data['email'])->get()->first();
            if ($consumer) {
                throw new ApiException('Извините, пользователь с таким электронным адресом уже зарегистрирован.');
            }
            
            $newConsumer = Consumers::create($data);

            $message = new Message();

            $letterId = ($newConsumer->part_type == 'online') ? 28 : 29;
            if (
                $newConsumer->part_type == 'offline'
                && !empty($request->input('user_id'))
                && isset($hashRecord->part_format)
                && $hashRecord->part_format == 'offline'
            ) {
                $letterId = 32;
                $newConsumer->status = 'accepted';
                $newConsumer->update();
            }

            if ($newConsumer->part_type == 'online') {
                $newConsumer->status = 'accepted';
                $newConsumer->update();
            }

            $message->setData([])->setTemplateById($letterId);
            Mailer::setTo($newConsumer->email);
            //  Mailer::setTo('alexandra.sedinkina@dentsu.ru');
            Mailer::send($message);

            return response()->json([
                'data' => [
                    'consumer' => $newConsumer->toArray(),
                ],
                'meta' => []
            ]);

        } catch (ApiException $e) {
            $e->toJSON();
        }
    }

    function createQuestion($request)
    {
        $data = $request->all();

        Feedbacks::create($data);
  
        return response()->json([
            'data' => $data,
            'meta' => []
        ]);
    }

    function vote($request)
    {
        $data = $request->all();

        if (!isset($data['variant_id'])) return;

        $variant = QuestionnairesVariants::find($data['variant_id']);
        if($variant) {
            $variant->score++;
            $variant->save();

//            QuestionnairesVariantsLogs::create([
//                'ip' => $_SERVER['REMOTE_ADDR'],
//                'questionnaires_id' => $variant->questionnaires_id,
//            ]);

            return response()->json([
                'data' => [],
                'meta' => []
            ]);
        }
    }
}