<?php
namespace App\Http\Components\Mailer;

use App\Http\Components\Mailer\PHPMailerSingleton;
use App\Models\Observers;
use App\Models\ObserversDefault;

/*
 * Decorator for PHPMailer
 */

class Mailer
{
    private static $mail;
    private static $sender;
    public static $recipients = [];

    function setFrom($address = null, $name = null)
    {
        self::$sender = [
            'address' => $address,
            'name' => $name,
        ];
    }

    /**
     * Adding item to recipients list
     *
     * @return void
     */
    static function setTo($email)
    {
        if (is_array($email)) {
            foreach ($email as $item) {
                self::$recipients[][] = $item[0];
            }
        } else {
            self::$recipients[][] = $email;
        }

    }
    
    static function setToByEventId($event_id, $entity_id)
    {
        $observers = Observers::where('business_event_id', $event_id)
            ->where('entity_id', $entity_id)
            ->join('auth.users', 'observers.user_id', 'users.id')
            ->get()
            ->toArray();

        foreach($observers as $observer) {$recipient=[];

            $recipient[0] = $observer['email'];
            $recipient[1] = $observer['name'];
            self::$recipients[] = $recipient;
        }
     
    }

    static function setToByDefault($event_id)
    {
        $observers = ObserversDefault::where('business_event_id', $event_id)
            ->join('auth.users', 'observers_default.user_id', 'users.id')
            ->get()
            ->toArray();

        foreach($observers as $observer) {$recipient=[];

            $recipient[0] = $observer['email'];
            $recipient[1] = $observer['name'];
            self::$recipients[] = $recipient;
        }
    }

    static function clearTo()
    {
        self::$recipients = [];
    }
    
    public static function send(Message $message)
    {
        try {

            self::$mail = PHPMailerSingleton::getInstance();
           // self::$mail->SMTPDebug = 2;
            # Кому
            self::$mail->clearAllRecipients();
            foreach (self::$recipients as $recipient) {
                self::$mail->addAddress(trim($recipient[0]));
            }

            # Content
            self::$mail->Subject = $message->getSubject();
            self::$mail->Body    = $message->getBody();

            if ($message->getAttachments()) {
                foreach ($message->attachments() as $attachment){
                    self::$mail->addAttachment($attachment['path'], $attachment['name']);
                }
            }

            if (self::$mail->send()) {
                self::clearTo();
                return true;

            } else {
                self::clearTo();
            }

        } catch (Exception $e)
        {
            echo 'Сообщение не было отправлено. Ошибка отправки: ', $e->msg;
        }
    }

    private function setSmtp()
    {
         $this->mail->addAddress('ellen@example.com');               // Name is optional
         $this->mail->addReplyTo('info@example.com', 'Information');
         $this->mail->addCC('cc@example.com');
         $this->mail->addBCC('bcc@example.com');

         $this->mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
    }

}