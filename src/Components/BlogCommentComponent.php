<?php

namespace App\Components;

use App\Repository\CommentsRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsTwigComponent('blog_comment')]
class BlogCommentComponent extends AbstractController {
    
    public int $id;

    public function __construct(private CommentsRepository $commentsRepository)
    {
        
    }



    public function getComment() {
        
        
        return $this->commentsRepository->findBy(['articles' => $this->id]);
    }
}