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
           // 'sendAboutTimingsOnline',
           // 'sendAboutTimingsOffline',
//            'importQrs',
//            'sendQrs',
//            'sendDayBeforeOnline',
            'sendHourBeforeOnline',
            'sendHourBeforeOffline',
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

    function importQrs()
    {
        $path = '/var/www/porabote/data/www/brandnewconference.ru.api/storage/qrs_consumers.xlsx';

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $countRow = $sheet->getHighestRow();

        for ($i = 2; $i <= $countRow; $i++) {

           // echo $sheet->getCell('E' . $i)->getValue() . PHP_EOL;
           // echo $sheet->getCell('K' . $i)->getValue();

            $consumer = Consumers::where('email', $sheet->getCell('E' . $i)->getValue())->get()->first();
            if ($consumer) {
                $consumer->qr_file_path = $sheet->getCell('K' . $i)->getValue();
             //   $consumer->save();
            } else {
                echo $sheet->getCell('E' . $i)->getValue();
            }


//            Hashes::create([
//                'hash' => $sheet->getCell('B' . $i)->getValue(),
//                'part_format' => $sheet->getCell('A' . $i)->getValue(),
//            ]);
        }
    }
    
    function sendQrs()
    {

        $consumers = Consumers::whereNotNull('qr_file_path')->where('status', 'accepted')->get()->toArray();
        $emails = [];
        foreach ($consumers as $consumer) {
            $emails[][] = $consumer['email'];
        }
        debug(count($emails));
        exit();
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


        foreach ($consumers as $consumer) {
//debug($consumer);
            $message = new Message();
            $msgData = ['qr_file_path' => 'https://brandnewconference.ru/images/qrs/qr_' . $consumer['qr_file_path'] . '.png'];
            $message->setData($msgData)->setTemplateById(36);
debug($consumer['email'] . ' -- ' . $consumer['qr_file_path']);
            Mailer::setTo([[$consumer['email']]]);//$email
         //   Mailer::send($message);
            Mailer::clearTo();
        }        
    }

    function sendDayBeforeOnline()
    {

        $consumers = Consumers::where('part_type', 'online')->get();
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

        $message = new Message();
        $msgData = [];
        $message
            ->setData($msgData)
            ->setTemplateById(40);

        foreach ($emails as $email) {
            Mailer::setTo([$email]);
           // Mailer::send($message);
            Mailer::clearTo();
        }
    }

    function sendHourBeforeOnline()
    {
        $consumers = Consumers::where('part_type', 'online')->get();
        $emails = [];
        foreach ($consumers as $consumer) {
            $emails[][] = $consumer['email'];
        }
//        debug(count($emails));
//        exit();

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

        $message = new Message();
        $msgData = [];
        $message
            ->setData($msgData)
            ->setTemplateById(38);

        foreach ($emails as $email) {
            Mailer::setTo([$email]);
            //Mailer::send($message);
            Mailer::clearTo();
        }
    }

    function sendHourBeforeOffline()
    {
        $consumers = Consumers::whereNotNull('qr_file_path')->where('status', 'accepted')->get()->toArray();
        $emails = [];
        foreach ($consumers as $consumer) {
            $emails[][] = $consumer['email'];
        }
        debug(count($emails));
        exit();


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

        foreach ($consumers as $consumer) {

            $message = new Message();
            $msgData = [
                'qr_file_path' => 'https://brandnewconference.ru/images/qrs/qr_' . $consumer['qr_file_path'] . '.png',
                'email' => '',//$consumer['email']
                ];//'qr_file_path' => 'https://brandnewconference.ru/images/qrs/qr_' . $consumer['qr_file_path'] . '.png'
            $message
                ->setData($msgData)
                ->setTemplateById(37);

            $email = $consumer['email'];
debug($email);
            Mailer::setTo([[$email]]);
          //  Mailer::send($message);
            Mailer::clearTo();
        }
    }

}