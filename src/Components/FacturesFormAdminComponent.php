<?php

namespace App\Components;

use App\Entity\Factures;
use App\Form\Admin\FacturesType;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsLiveComponent('factures_form_admin')]
class FacturesFormAdminComponent extends AbstractController {

    use LiveCollectionTrait;
    use DefaultActionTrait;

    #[LiveProp]
    public ?Factures $facture = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(FacturesType::class, $this->facture);
    }
}