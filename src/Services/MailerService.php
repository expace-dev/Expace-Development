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
        $to = 'noreply@expace-development.fr',
        $attache = '',
        $type = '',
        $client = '',
        $document = ''
        ):void {

        $email = (new TemplatedEmail())
            ->from(new Address($from, $name))
            ->to($to)
            ->subject($subject)
            ->htmlTemplate($template)
            ->context([
                'message' => $content
            ]);

        $email = (new TemplatedEmail())
            ->from(new Address($from, $name))
            ->to(new Address($to))
            ->subject($subject)
            ->attachFromPath($attache)
            ->htmlTemplate($template)
            ->context([
                'prenom' => $client,
                'document' => $document
        ]);

        $this->mailer->send($email);

    }

    public function sendDocument(
        $from = '', 
        $name = '', 
        $template = '', 
        $subject = '', 
        $content = '', 
        $to = 'noreply@expace-development.fr',
        $attache = '',
        $docAttache = '',
        $client = '',
        $document = '',
        $mime = ''
        ):void {

        $email = (new TemplatedEmail())
            ->from(new Address($from, $name))
            ->to(new Address($to))
            ->subject($subject)
            ->attachFromPath($attache, $docAttache, $mime)
            ->htmlTemplate($template)
            ->context([
                'prenom' => $client,
                'document' => $document
        ]);

        $this->mailer->send($email);

    }
}