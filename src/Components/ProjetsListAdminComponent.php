<?php

namespace App\Components;

use App\Repository\ProjetsRepository;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent('projets_list_admin')]
class ProjetsListAdminComponent {

    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public ?string $query = null;




    public function __construct(private ProjetsRepository $projetsRepository, private RequestStack $requestStack)
    {
    }

    public function getAllProjets(): array {

        $request = $this->requestStack->getCurrentRequest();
        
        $page = $request->query->getInt('page', 1);

        //$donnees = $articlesRepository->findArticles($page, 5);
        return $this->projetsRepository->findProjets($page, 15);
        //return $this->articlesRepository->findArticles($page, 5);
    }
    
}