<?php

namespace App\Services;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class MailerService {
    public function __construct(private MailerInterface $mailer)
    {
        
    }

    public function sendEmail(
        $from = '', 
        $name = '', 
        $template = '', 
        $subject = '', 
        $content = '', 
        $to = 'admin@expace-development.fr'
        ):void {

        $email = (new TemplatedEmail())
            ->from(new Address($from, $name))
            ->to($to)
            ->subject($subject)
            ->htmlTemplate($template)
            ->context([
                'message' => $content
            ]);

        $this->mailer->send($email);

    }
}