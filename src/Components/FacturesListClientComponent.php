<?php

namespace App\Components;

use App\Repository\FacturesRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('factures_list_client')]
class FacturesListClientComponent {

    use DefaultActionTrait;

    public function __construct(private FacturesRepository $facturesRepository, private RequestStack $requestStack, private Security $security)
    {
    }

    public function getAllFactures(): array {

        $request = $this->requestStack->getCurrentRequest();
        
        $page = $request->query->getInt('page', 1);

        $user = $this->security->getUser();

        return $this->facturesRepository->findFacturesClient($page, 15, $user);
    }
    
}