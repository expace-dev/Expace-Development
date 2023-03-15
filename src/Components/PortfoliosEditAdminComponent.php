<?php

namespace App\Components;

use App\Entity\Portfolios;
use App\Form\Admin\PortfoliosEditType;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsLiveComponent('portfolios_edit_admin')]
class PortfoliosEditAdminComponent extends AbstractController {

    use LiveCollectionTrait;
    use DefaultActionTrait;

    #[LiveProp]
    public ?Portfolios $portfolio = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(PortfoliosEditType::class, $this->portfolio);
    }
}