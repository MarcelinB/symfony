<?php

namespace App\Service;

use Twig\Environment;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpClient\Exception\TransportException;

class MailerService
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @param MailerInterface    $mailer
     * @param Environment   $twig
     */
    public function __construct(MailerInterface $mailer, Environment  $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function sendEmail(
        string $subject,
        string $mailEnvoi,
        string $mailreception,
        string $template,
        array $param = NULL
    ): void {
        try {
            $email = (new Email())
                ->from($mailEnvoi)
                ->to($mailreception)
                ->subject($subject)
                ->html($this->twig->render($template, $param), 'UTF-8');

            $this->mailer->send($email);
        } catch (TransportException $e) {
        }
    }
}
