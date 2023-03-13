<?php

namespace App\Components;

use App\Entity\Messages;
use App\Form\Client\MessagesType;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsLiveComponent('messages_form_client')]
class MessagesFormClientComponent extends AbstractController {

    use LiveCollectionTrait;
    use DefaultActionTrait;

    #[LiveProp]
    public ?Messages $message = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(MessagesType::class, $this->message);
    }
}