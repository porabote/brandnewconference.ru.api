<?php

namespace App\Http\Controllers;

use App\Http\Components\Mailer\Mailer;
use App\Http\Components\Mailer\Message;

class MailerController extends Controller
{

    function sendTest()
    {
        $message = new Message();
        $msgData = ['id' => '99'];
        $message
            ->setData($msgData)
            ->setTemplateById(1);
        
        Mailer::setTo([
                //['Andreev.Nikolaj@ts-gruppe.com'],
                ['maksimov_den@mail.ru']
            ]);
        Mailer::send($message);
    }

}