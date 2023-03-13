<?php

namespace App\Components;

use App\Repository\FacturesRepository;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent('factures_list_admin')]
class FacturesListAdminComponent {

    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public ?string $query = null;




    public function __construct(private FacturesRepository $facturesRepository, private RequestStack $requestStack)
    {
    }

    public function getAllFactures(): array {

        $request = $this->requestStack->getCurrentRequest();
        
        $page = $request->query->getInt('page', 1);

        //$donnees = $articlesRepository->findArticles($page, 5);
        return $this->facturesRepository->findFactures($page, 15);
        //return $this->articlesRepository->findArticles($page, 5);
    }
    
}