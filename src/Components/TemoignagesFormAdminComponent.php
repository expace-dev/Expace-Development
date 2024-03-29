<?php

namespace App\Components;

use App\Entity\Temoignages;
use App\Form\Admin\TemoignagesType;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsLiveComponent('temoignages_form_admin')]
class TemoignagesFormAdminComponent extends AbstractController {

    use LiveCollectionTrait;
    use DefaultActionTrait;

    #[LiveProp]
    public ?Temoignages $temoignage = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(TemoignagesType::class, $this->temoignage);
    }
}