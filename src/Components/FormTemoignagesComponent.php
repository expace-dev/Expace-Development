<?php

namespace App\Components;

use App\Entity\Devis;
use App\Form\DevisType;
use App\Entity\Factures;
use App\Entity\Portfolios;
use App\Entity\Temoignages;
use App\Form\Admin\TemoignagesType;
use App\Form\FacturesType;
use App\Form\PortfoliosType;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsLiveComponent('form_temoignages')]
class FormTemoignagesComponent extends AbstractController {

    use LiveCollectionTrait;
    use DefaultActionTrait;

    #[LiveProp]
    public ?Temoignages $temoignage = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(TemoignagesType::class, $this->temoignage);
    }
}