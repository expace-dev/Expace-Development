<?php

namespace App\Components;

use App\Repository\PortfoliosRepository;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent('portfolios_list_admin')]
class PortfoliosListAdminComponent {

    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public ?string $query = null;




    public function __construct(private PortfoliosRepository $portfoliosRepository, private RequestStack $requestStack)
    {
    }

    public function getAllPortfolios(): array {

        $request = $this->requestStack->getCurrentRequest();
        
        $page = $request->query->getInt('page', 1);

        //$donnees = $articlesRepository->findArticles($page, 5);
        return $this->portfoliosRepository->findPortfolios($page, 15);
        //return $this->articlesRepository->findArticles($page, 5);
    }
    
}