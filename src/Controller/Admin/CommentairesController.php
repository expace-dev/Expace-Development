<?php

namespace App\Controller\Admin;

use App\Entity\Comments;
use App\Form\Blog\Admin\CommentsType;
use App\Repository\CommentsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/blog/commentaires')]
class CommentairesController extends AbstractController
{
    #[Route('/', name: 'app_admin_comments_blog_index', methods: ['GET'])]
    public function commentsIndex(CommentsRepository $commentsRepository): Response
    {
        return $this->render('admin/blog/commentaires/index.html.twig', [
            'comments' => $commentsRepository->findAll(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_comments_blog_edit', methods: ['GET', 'POST'])]
    public function commentsEdit(Request $request, Comments $comment, CommentsRepository $commentsRepository): Response
    {
        $form = $this->createForm(CommentsType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentsRepository->save($comment, true);

            $this->addFlash('success', '<span class="me-2 fa fa-circle-check"></span>Votre commentaire a été enregistré avec succès');

            return $this->redirectToRoute('app_admin_comments_blog_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/blog/commentaires/edit.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_admin_comments_blog_delete', methods: ['GET'])]
    public function commentsDelete(Comments $comment, CommentsRepository $commentsRepository): Response
    {
            $commentsRepository->remove($comment, true);

            $this->addFlash('success', '<span class="me-2 fa fa-circle-check"></span>Votre commentaire a été supprimé avec succès');

        return $this->redirectToRoute('app_admin_comments_blog_index', [], Response::HTTP_SEE_OTHER);
    }
}
