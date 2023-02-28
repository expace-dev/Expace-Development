<?php

namespace App\Components;

use App\Entity\Projets;
use App\Form\Client\ProjetsType;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsLiveComponent('projets_form_client')]
class ProjetsFormClientComponent extends AbstractController {

    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp(fieldName: 'data')]
    public ?Projets $projet = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(ProjetsType::class, $this->projet);
    }
    
}