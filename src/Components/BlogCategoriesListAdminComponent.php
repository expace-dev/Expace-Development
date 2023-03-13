<?php

namespace App\Components;

use App\Repository\CategoriesRepository;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

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