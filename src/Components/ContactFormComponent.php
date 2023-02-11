<?php

namespace App\Components;

use App\Entity\Contact;
use App\Form\ContactType;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsLiveComponent('contact_form')]
class ContactFormComponent extends AbstractController {


    use DefaultActionTrait;
    use ComponentWithFormTrait;


    #[LiveProp(fieldName: 'data')]
    public ?Contact $contact = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(ContactType::class, $this->contact);
    }

}