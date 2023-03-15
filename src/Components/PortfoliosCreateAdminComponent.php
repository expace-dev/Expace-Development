<?php

namespace App\Components;

use App\Entity\Portfolios;
use App\Form\Admin\PortfoliosCreateType;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsLiveComponent('portfolios_create_admin')]
class PortfoliosCreateAdminComponent extends AbstractController {

    use LiveCollectionTrait;
    use DefaultActionTrait;

    #[LiveProp]
    public ?Portfolios $portfolio = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(PortfoliosCreateType::class, $this->portfolio);
    }
}