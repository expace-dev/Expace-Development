<?php

namespace App\Components;

use App\Entity\Users;
use App\Form\ProfilCreateType;
use App\Form\ProfilUpdateCredentialsType;
use App\Form\ProfilUpdateType;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsLiveComponent('profil_form_credentials')]
class ProfilFormCredentialsComponent extends AbstractController {

    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp(fieldName: 'data')]
    public ?Users $user = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(ProfilUpdateCredentialsType::class, $this->user);
    }
    
}