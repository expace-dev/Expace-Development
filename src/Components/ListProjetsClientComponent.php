<?php

namespace App\Components;

use App\Repository\UsersRepository;
use App\Repository\ProjetsRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsLiveComponent('list_projets_client')]
class ListProjetsClientComponent {

    use DefaultActionTrait;

    public function __construct(private ProjetsRepository $projetsRepository, private RequestStack $requestStack, private Security $security)
    {
    }

    public function getAllProjets(): array {

        $request = $this->requestStack->getCurrentRequest();
        
        $page = $request->query->getInt('page', 1);

        $user = $this->security->getUser();

        return $this->projetsRepository->findProjetsClient($page, 15, $user);
    }
    
}