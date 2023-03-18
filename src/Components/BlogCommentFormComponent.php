<?php

namespace App\Components;

use App\Entity\Comments;
use App\Form\Blog\Client\CommentType;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsLiveComponent('blog_comment_form')]
class BlogCommentFormComponent extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp(fieldName: 'data')]
    public ?Comments $commentaires = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(CommentType::class, $this->commentaires);
    }
}