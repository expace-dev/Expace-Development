<?php

namespace App\Components;

use App\Repository\PortfoliosRepository;
use App\Repository\UsersRepository;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsLiveComponent('portfolios_all')]
class PortfoliosAllComponent {

    use DefaultActionTrait;



    public function __construct(private PortfoliosRepository $portfoliosRepository, private RequestStack $requestStack)
    {
    }

    public function getAllPortfolios(): array {

        $request = $this->requestStack->getCurrentRequest();
        
        $page = $request->query->getInt('page', 1);

        //$donnees = $articlesRepository->findArticles($page, 5);
        return $this->portfoliosRepository->findPortfolios($page, 9);
        //return $this->articlesRepository->findArticles($page, 5);
    }
    
}