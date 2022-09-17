<?php

namespace App\Http\Controllers;

use App\Http\Components\Mailer\Mailer;
use App\Http\Components\Mailer\Message;
use App\Models\Consumers;

class MailerController extends Controller
{

    static $authAllows;
    private $authData = [];

    function __construct()
    {
        self::$authAllows = [
            'sendAboutTimingsOnline',
            'sendAboutTimingsOffline',
            'testMailStatus'
        ];
    }

    function sendAboutTimingsOnline()
    {

        $consumers = Consumers::where('part_type', 'online')->get();
        $emails = [];
        foreach ($consumers as $consumer) {
            $emails[][] = $consumer['email'];
        }
        debug(count($emails));
//        debug($emails);
//exit();
//        $emails = [
//            ['valeria.dunets@dentsu.ru'],
//            ['faithix9@gmail.com'],
//            ['valeria.dunets@yandex.ru'],
//            ['anastas.sarkisyan@dentsu.ru'],
//            ['anastassarkisyan@gmail.com'],
//            ['sedinkina.a@gmail.com'],
//            ['sedinkina.a.ya@yandex.ru'],
//            ['maksimov_den@mail.ru'],
//            ['maksimov.dev@gmail.com'],
//        ];

        $message = new Message();
        $msgData = ['id' => '99'];
        $message
            ->setData($msgData)
            ->setTemplateById(35);

//        foreach ($emails as $email) {
//            Mailer::setTo([$email]);
//            Mailer::send($message);
//            Mailer::clearTo();
//        }
    }

    function sendAboutTimingsOffline()
    {
        $consumers = Consumers::where('part_type', 'offline')->where('status', 'accepted')->get();
        $emails = [];
        foreach ($consumers as $consumer) {
            $emails[][] = $consumer['email'];
        }
        debug(count($emails));
//
//        $emails = [
//            ['maksimov_den@mail.ru'],
//            ['valeria.dunets@dentsu.ru'],
//            ['faithix9@gmail.com'],
//            ['valeria.dunets@yandex.ru'],
//            ['anastas.sarkisyan@dentsu.ru'],
//            ['anastassarkisyan@gmail.com'],
//            ['sedinkina.a@gmail.com'],
//            ['sedinkina.a.ya@yandex.ru'],
//            ['maksimov.dev@gmail.com'],
//        ];
//        debug($emails);
//        exit();

        $message = new Message();
        $msgData = ['id' => '99'];
        $message
            ->setData($msgData)
            ->setTemplateById(34);

//        foreach ($emails as $email) {
//            Mailer::setTo([$email]);
//            Mailer::send($message);
//            Mailer::clearTo();
//        }
    }

    function testMailStatus()
    {
        $emails = [
            ['maksimov_den@mail88.ru'],
//            ['valeria.dunets@dentsu.ru'],
//            ['faithix9@gmail.com'],
//            ['valeria.dunets@yandex.ru'],
//            ['anastas.sarkisyan@dentsu.ru'],
//            ['anastassarkisyan@gmail.com'],
//            ['sedinkina.a@gmail.com'],
//            ['sedinkina.a.ya@yandex.ru'],
//            ['maksimov.dev@gmail.com'],
        ];

        $message = new Message();
        $msgData = ['id' => '99'];
        $message
            ->setData($msgData)
            ->setTemplateById(34);

        foreach ($emails as $email) {
            Mailer::setTo([$email]);
            Mailer::send($message);
//            $errorMessage = error_get_last();
//            debug($errorMessage);
            Mailer::clearTo();
        }
    }

}