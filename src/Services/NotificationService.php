<?php

namespace App\Services;

use DateTime;
use App\Entity\Notifications;
use App\Repository\NotificationsRepository;
use App\Repository\UsersRepository;

class NotificationService {

    public function __construct(
        private NotificationsRepository $notificationsRepository, 
        private MailerService $mailer,
        private UsersRepository $usersRepository
    )
    {
        
    }

    public function addNotification($sender, $recipient, $message, $document, $type) {

        
        $notification = new Notifications();

        $notification->setSender($sender)
                         ->setRecipient($recipient)
                         ->setMessage($message)
                         ->setDocument($document)
                         ->setCreatedAt(new DateTime())
                         ->setType($type);

            $this->notificationsRepository->save($notification, true);

            $this->mailer->sendNotification(
                from: 'noreply@expace-development.fr',
                name: 'Expace Development',
                to: $recipient->getEmail(),
                template: 'emails/_new_doc.html.twig',
                subject: 'Nouvelle notification',
                client: $recipient->getPrenom(),
                notification: $message
            );

    }
}