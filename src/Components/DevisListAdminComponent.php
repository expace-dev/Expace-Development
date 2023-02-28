<?php

namespace App\Components;

use App\Repository\DevisRepository;
use App\Repository\ProjetsRepository;
use App\Repository\UsersRepository;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsLiveComponent('devis_list_admin')]
class DevisListAdminComponent {

    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public ?string $query = null;




    public function __construct(private DevisRepository $devisRepository, private RequestStack $requestStack)
    {
    }

    public function getAllDevis(): array {

        $request = $this->requestStack->getCurrentRequest();
        
        $page = $request->query->getInt('page', 1);

        //$donnees = $articlesRepository->findArticles($page, 5);
        return $this->devisRepository->findDevis($page, 15);
        //return $this->articlesRepository->findArticles($page, 5);
    }
    
}