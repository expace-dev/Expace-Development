<?php

namespace App\Components;

use App\Entity\Devis;
use App\Form\DevisType;
use App\Entity\Factures;
use App\Entity\Portfolios;
use App\Form\FacturesType;
use App\Form\PortfoliosType;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsLiveComponent('portfolios_form_admin')]
class PortfoliosFormAdminComponent extends AbstractController {

    use LiveCollectionTrait;
    use DefaultActionTrait;

    #[LiveProp]
    public ?Portfolios $portfolio = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(PortfoliosType::class, $this->portfolio);
    }
}