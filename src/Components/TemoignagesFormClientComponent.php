<?php

namespace App\Components;

use App\Entity\Temoignages;
use App\Form\Client\TemoignagesType;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsLiveComponent('temoignages_form_client')]
class TemoignagesFormClientComponent extends AbstractController {

    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp(fieldName: 'data')]
    public ?Temoignages $temoignage = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(TemoignagesType::class, $this->temoignage);
    }
    
}