<?php
namespace App\Http\Components\Mailer;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class PHPMailerSingleton {

    private static $instance;

    private function __construct(){}

    static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new \PHPMailer\PHPMailer\PHPMailer(true);
            self::setDefaultParams();
        }
        return self::$instance;
    }

    static function setDefaultParams()
    {
        self::$instance->CharSet = 'UTF-8';
        self::$instance->isHTML(true);
        self::$instance->setFrom(
            \Config::get('mail.from.address'),
            \Config::get('mail.from.name')
        );

//        $this->mail->SMTPDebug = 2;                                 // Enable verbose debug output
//        $this->mail->isSMTP();                                      // Set mailer to use SMTP
//        $this->mail->Host = SMTP_HOST;  // Specify main and backup SMTP servers
//        $this->mail->SMTPAuth = (!isset($options['smtp_auth'])) ? false : true; // Enable SMTP authentication
//        $this->mail->Username = SMTP_USERNAME;                 // SMTP username
//        $this->mail->Password = SMTP_PASSWORD;                           // SMTP password
//        //$this->mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
//        $this->mail->Port = SMTP_PORT;                                    // TCP port to connect to
    }

}

?>