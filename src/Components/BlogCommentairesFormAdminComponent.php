<?php

namespace App\Components;

use App\Entity\Comments;
use App\Form\Blog\Admin\CommentsType;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsLiveComponent('blog_commentaires_form_admin')]
class BlogCommentairesFormAdminComponent extends AbstractController {

    use LiveCollectionTrait;
    use DefaultActionTrait;

    #[LiveProp]
    public ?Comments $comment = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(CommentsType::class, $this->comment);
    }
}