<?php

namespace App\Components;

use App\Repository\PortfoliosRepository;
use App\Repository\TemoignagesRepository;
use App\Repository\UsersRepository;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsLiveComponent('list_temoignages')]
class ListTemoignagesComponent {

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