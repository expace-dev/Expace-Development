<?php

namespace App\Components;

use App\Repository\TemoignagesRepository;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('temoignages_list_admin')]
class TemoignagesListAdminComponent {

    use DefaultActionTrait;

    public function __construct(private TemoignagesRepository $temoignagesRepository, private RequestStack $requestStack)
    {
    }

    public function getAllTemoignages(): array {

        $request = $this->requestStack->getCurrentRequest();
        
        $page = $request->query->getInt('page', 1);

        return $this->temoignagesRepository->findTemoignages($page, 15);
    }
    
}