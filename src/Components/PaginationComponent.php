<?php

namespace App\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('pagination')]
class PaginationComponent {

    public string $pages;
    public string $currentPage;
    public string $limit;
    public string $path;
    public string $position;

}