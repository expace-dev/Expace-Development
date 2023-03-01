<?php

namespace App\Components;

use App\Repository\ArticlesRepository;
use App\Repository\CategoriesRepository;
use App\Repository\CommentsRepository;
use App\Repository\ProjetsRepository;
use App\Repository\UsersRepository;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsLiveComponent('blog_commentaires_list_admin')]
class BlogCommentairesListAdminComponent {

    use DefaultActionTrait;

    public function __construct(private CommentsRepository $commentsRepository, private RequestStack $requestStack)
    {
    }

    public function getAllCommentaires(): array {

        $request = $this->requestStack->getCurrentRequest();
        
        $page = $request->query->getInt('page', 1);

        return $this->commentsRepository->findComments($page, 15);
    }
    
}