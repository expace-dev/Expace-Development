<?php

namespace App\Components;

use App\Repository\PaiementsRepository;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('paiements_list_admin')]
class PaiementsListAdminComponent {

    use DefaultActionTrait;

    public function __construct(private PaiementsRepository $paiementsRepository, private RequestStack $requestStack)
    {
    }

    public function getAllPaiements(): array {

        $request = $this->requestStack->getCurrentRequest();
        
        $page = $request->query->getInt('page', 1);

        return $this->paiementsRepository->findPaiements($page, 15);
    }
    
}