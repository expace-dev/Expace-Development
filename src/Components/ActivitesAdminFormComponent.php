<?php

namespace App\Components;

use App\Entity\Activites;
use App\Form\ActivitesType;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsLiveComponent('activites_admin_form')]
class ActivitesAdminFormComponent extends AbstractController {

    use LiveCollectionTrait;
    use DefaultActionTrait;

    #[LiveProp]
    public ?Activites $activite = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(ActivitesType::class, $this->activite);
    }
}