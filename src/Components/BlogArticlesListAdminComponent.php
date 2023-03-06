<?php

namespace App\Components;

use App\Repository\ArticlesRepository;
use App\Repository\ProjetsRepository;
use App\Repository\UsersRepository;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsLiveComponent('blog_articles_list_admin')]
class BlogArticlesListAdminComponent {

    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public ?string $query = null;




    public function __construct(private ArticlesRepository $articlesRepository, private RequestStack $requestStack)
    {
    }

    public function getAllArticles(): array {

        $request = $this->requestStack->getCurrentRequest();
        
        $page = $request->query->getInt('page', 1);

        return $this->articlesRepository->findArticles($page, 15);
    }
    
}