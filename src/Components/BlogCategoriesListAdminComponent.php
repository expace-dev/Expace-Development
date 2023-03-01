<?php

namespace App\Components;

use App\Repository\ArticlesRepository;
use App\Repository\CategoriesRepository;
use App\Repository\ProjetsRepository;
use App\Repository\UsersRepository;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsLiveComponent('blog_categories_list_admin')]
class BlogCategoriesListAdminComponent {

    use DefaultActionTrait;

    public function __construct(private CategoriesRepository $categoriesRepository, private RequestStack $requestStack)
    {
    }

    public function getAllCategories(): array {

        $request = $this->requestStack->getCurrentRequest();
        
        $page = $request->query->getInt('page', 1);

        return $this->categoriesRepository->findCategories($page, 15);
    }
    
}