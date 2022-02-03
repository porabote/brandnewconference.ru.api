<?php
namespace App\Http\Components\Mailer;

use Illuminate\Support\Facades\DB;
use Porabote\Auth\Auth;

class Message {

    private $twig;
    protected $template;
    private $subject;
    private $body;
    private $attachments;

    public function setTemplateById($template_id)
    {
        $template = DB::table('api.mails_patterns')->find($template_id);
        $this->setSubject($template->subject);
        $this->setTemplate($template->body);

        $this->setTwig();

        return $this;
    }

    private function setTwig()
    {
        $loader = new \Twig\Loader\ArrayLoader([
            'subject_html' => $this->subject,
            'body_html' => $this->template
        ]);

        $this->twig = new \Twig\Environment($loader);
        $this->twig->addExtension(new \Twig\Extra\Intl\IntlExtension());
        $this->twig->getExtension(\Twig\Extension\CoreExtension::class)->setTimezone('Europe/Moscow');
    }

    public function setTemplate($template)
    {
        $this->template = $template;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    public function getSubject()
    {
        return $this->twig->render('subject_html', $this->data);
    }

    public function getBody()
    {
        return $this->setBody();
    }

    private function setBody()
    {
        return $this->twig->render('body_html', $this->data);
    }

    public function getAttachments()
    {
        return null;
    }

    public function setAttachments($files)
    {
        $this->attachments = $files;
        return $this;
    }

    public function setData($data)
    {
        $this->data = $data;

        $accounts = [
            'Thyssen' => 'Норильск',
            'Solikamsk' => 'Соликамск',
        ];

        $this->data['platform']['en_alias'] = Auth::$user->account_alias;
        $this->data['platform']['ru_alias'] = $accounts[Auth::$user->account_alias];
        return $this;
    }

}