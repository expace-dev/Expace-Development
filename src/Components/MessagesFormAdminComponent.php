<?php

namespace App\Components;

use App\Entity\Devis;
use App\Form\DevisType;
use App\Entity\Messages;
use App\Form\Admin\MessagesType;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsLiveComponent('messages_form_admin')]
class MessagesFormAdminComponent extends AbstractController {

    use LiveCollectionTrait;
    use DefaultActionTrait;

    #[LiveProp]
    public ?Messages $message = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(MessagesType::class, $this->message);
    }
}