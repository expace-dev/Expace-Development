<?php

namespace App\Components;

use App\Repository\ArticlesRepository;
use App\Repository\CategoriesRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('blog_post_recent')]
class BlogPostRecentComponent {

    private $articlesRepository;


    public function __construct(ArticlesRepository $articlesRepository)
    {
        $this->articlesRepository = $articlesRepository;
    }

    public function getRecentArticle(): array {

        return $this->articlesRepository->findBy([], ['date' => 'DESC'], 10);
    }
    
}