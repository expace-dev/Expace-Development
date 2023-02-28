<?php

namespace App\Components;

use App\Entity\Articles;
use App\Repository\ArticlesRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('blog_post')]
class BlogPostComponent {
    
    public int $id;

    public function __construct(private ArticlesRepository $articlesRepository)
    {
        
    }

    public function getBlogpost(): Articles {

        return $this->articlesRepository->find($this->id);

    }
}