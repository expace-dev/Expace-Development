<?php

namespace App\Components;

use App\Repository\CommentsRepository;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

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