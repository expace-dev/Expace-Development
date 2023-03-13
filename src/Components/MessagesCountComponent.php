<?php

namespace App\Components;

use App\Repository\MessagesRepository;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('messages_count')]
class MessagesCountComponent {

    use DefaultActionTrait;

    #[LiveProp(fieldName: 'data')]
    public $id;

    public function __construct(private MessagesRepository $messagesRepository, protected RequestStack $requestStack)
    {
    }

    public function getAllMessages(): int {
        return $this->messagesRepository->count([
            'recipient' => $this->id,
            'lu' => false
        ]);
    }
    
}