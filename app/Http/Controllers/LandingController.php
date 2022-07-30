<?php

namespace App\Http\Controllers;

use App\Http\Components\Mailer\Mailer;
use App\Http\Components\Mailer\Message;
use Porabote\Auth\Auth;
use Porabote\FullRestApi\Server\ApiTrait;
use App\Models\Speakers;
use App\Models\Faq;
use App\Models\Consumers;
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
        ];
    }

    function get()
    {
        $speakers = Speakers::orderBy('lft')->with('avatar')->get();
        $faqs = Faq::orderBy('lft')->get();

        return response()->json([
            'data' => [
                'speakers' => $speakers,
                'faqs' => $faqs,
            ],
            'meta' => []
        ]);
    }

    function registration($request)
    {
        try {
            $data = $request->all();

            foreach (Consumers::$requiredFields as $requiredField) {
                if (!isset($data[$requiredField])) {
                    throw new ApiException('Пожалуйста, заполните все обязательные поля.');
                }
            }

            $consumer = Consumers::where('email', $data['email'])->get()->first();
            if ($consumer) {
                throw new ApiException('Извините, пользователь с таким электронным адресом уже зарегистрирован.');
            }

            $newConsumer = Consumers::create($data);

            $message = new Message();

            $letterId = ($newConsumer->part_type == 'online') ? 28 : 29;

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
}