<?php

namespace App\Components;

use App\Repository\NotificationsRepository;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('notifications_count')]
class NotificationsCountComponent {

    use DefaultActionTrait;

    #[LiveProp(fieldName: 'data')]
    public $id;

    public function __construct(private NotificationsRepository $notificationsRepository, protected RequestStack $requestStack)
    {
    }

    public function getAllNotifications(): int {
        return $this->notificationsRepository->count([
            'recipient' => $this->id,
            'lu' => false
        ]);
    }
    
}