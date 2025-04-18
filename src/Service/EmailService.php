<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class EmailService
{
    public function __construct(
        private MailerInterface $mailer
    ) {}

    public function sendConfirmationEmail(string $to, string $name, string $subject): void
    {
        $email = (new TemplatedEmail())
            ->from('noreply@gustora.com')
            ->to($to)
            ->subject('Thank you for contacting Gustora Restaurant')
            ->htmlTemplate('emails/contact_confirmation.html.twig')
            ->context([
                'name' => $name,
                'subject' => $subject,
                'date' => new \DateTime()
            ]);

        $this->mailer->send($email);
    }
}