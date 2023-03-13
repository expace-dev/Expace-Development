<?php

namespace App\Components;

use App\Entity\Devis;
use App\Form\Admin\DevisType;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsLiveComponent('devis_form_admin')]
class DevisFormAdminComponent extends AbstractController {

    use LiveCollectionTrait;
    use DefaultActionTrait;

    #[LiveProp]
    public ?Devis $devi = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(DevisType::class, $this->devi);
    }
}