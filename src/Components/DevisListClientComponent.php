<?php

namespace App\Components;

use App\Repository\DevisRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('devis_list_client')]
class DevisListClientComponent {

    use DefaultActionTrait;

    public function __construct(private DevisRepository $devisRepository, private RequestStack $requestStack, private Security $security)
    {
    }

    public function getAllDevis(): array {

        $request = $this->requestStack->getCurrentRequest();
        
        $page = $request->query->getInt('page', 1);

        $user = $this->security->getUser();

        return $this->devisRepository->findDevisClient($page, 15, $user);
    }
    
}