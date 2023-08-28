<?php

namespace Application\Service;

use Application\Entity\Client;
use Laminas\Mail\Message;
use Laminas\Mail\Transport\Smtp as SmtpTransport;
use Laminas\Mail\Transport\SmtpOptions;
use Laminas\Mime\Message as MimeMessage;
use Laminas\Mime\Part as MimePart;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use ZendTwig\Renderer\TwigRenderer;

class MailService
{
    const TYPE_PASSWORD_CHANGED = 'passwordChanged';

    private array $config;
    private TwigRenderer $renderer;

    private array $smtpTransportConfig;
    private $from;

    private bool $throwExceptions = false;


    public function __construct(array $config, TwigRenderer $renderer)
    {
        $this->config = $config;
        $this->renderer = $renderer;

        $this->from = $config['smtp']['from'];
        unset($config['smtp']['from']);
        $this->smtpTransportConfig = $config['smtp'];
    }



    /**
     * @param string $content
     * @param $emailType
     * @param array|null $data
     * @param $type
     * @return bool
     * @throws \Exception
     */
    public function send(string $content, string $recipient, string $type = 'text'): bool
    {

        $transport = new SmtpTransport();
        $smtpOptions = new SmtpOptions($this->smtpTransportConfig);
        $transport->setOptions($smtpOptions);

        $message = new Message();
        $message->setEncoding('UTF-8');
        $message->setFrom($this->from['email'], $this->from['name']);
        $message->setSubject('rekrutacja - Zmiana hasła do panelu');
        $message->setBody($this->createBody($content, $type));
        $message->addTo($recipient);

        try {
            $transport->send($message);
        } catch (\Exception $e) {
            if ($this->isThrowExceptions()) {
                throw $e;
            }
            return false;
        }
        return true;
    }



    /**
     * @param string $content
     * @param string $type text or html
     * @return MimeMessage
     */
    private function createBody(string $content, string $type = 'text'): MimeMessage
    {
        $part = new MimePart($content);

        if ($type == 'html') {
            $part->type = 'text/html';
        } else {
            $part->type = "text/plain";
        }

        $part->charset = 'utf-8';
        $parts[] = $part;

        $body = new MimeMessage();
        $body->setParts($parts);
        return $body;
    }

    /**
     * @return bool
     */
    public function isThrowExceptions(): bool
    {
        return $this->throwExceptions;
    }

    /**
     * @param bool $throwExceptions
     */
    public function setThrowExceptions(bool $throwExceptions): void
    {
        $this->throwExceptions = $throwExceptions;
    }
}
