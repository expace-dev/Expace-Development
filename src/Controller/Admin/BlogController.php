<?php

namespace App\Controller\Admin;

use App\Entity\Articles;
use App\Services\UploadService;
use App\Form\Blog\Admin\ArticlesType;
use App\Repository\ArticlesRepository;
use App\Repository\CategoriesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/blog')]
class BlogController extends AbstractController
{
    #[Route('/', name: 'app_admin_blog_index', methods: ['GET'])]
    public function index(ArticlesRepository $articlesRepository): Response
    {
        return $this->render('admin/blog/index.html.twig', [
            'articles' => $articlesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_blog_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ArticlesRepository $articlesRepository, UploadService $uploadService): Response
    {
        $article = new Articles();
        $form = $this->createForm(ArticlesType::class, $article);
        $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                if ($form->get('img')->getData()) {
                    $fichier = $form->get('img')->getData();
                    $directory = 'blog_directory';
                    $article->setImg('images/blog/' .$uploadService->send($fichier, $directory))
                            ->setAuteur($this->getUser());


                    $articlesRepository->save($article, true);

                    $this->addFlash('success', '<span class="me-2 fa fa-circle-check"></span>Votre article a été enregistré avec succès');

                    return $this->redirectToRoute('app_admin_blog_index', [], Response::HTTP_SEE_OTHER);
                }
                else {
                    $this->addFlash('danger', '<span class="me-2 fa fa-circle-exclamation"></span>Veuillez fournir une image d\'illustration');
                }
            }

            if ($form->isSubmitted() && !$form->isValid()) {
                $this->addFlash('danger', '<span class="me-2 fa fa-circle-exclamation"></span>Des erreurs subsistent veuillez corriger votre saisie');
            }

        return $this->render('admin/blog/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_blog_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Articles $article, ArticlesRepository $articlesRepository, UploadService $uploadService): Response
    {
        $form = $this->createForm(ArticlesType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($form->get('img')->getData()) {
                $fichier = $form->get('img')->getData();
                $directory = 'blog_directory';
                unlink($article->getImg());
                $article->setImg('images/blog/' .$uploadService->send($fichier, $directory));
            }
            

            $articlesRepository->save($article, true);

            $this->addFlash('success', '<span class="me-2 fa fa-circle-check"></span>Votre article a été enregistré avec succès');

            return $this->redirectToRoute('app_admin_blog_index', [], Response::HTTP_SEE_OTHER);
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('danger', '<span class="me-2 fa fa-circle-exclamation"></span>Des erreurs subsistent veuillez corriger votre saisie');
        }

        return $this->render('admin/blog/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_admin_blog_delete', methods: ['GET'])]
    public function delete(Request $request, Articles $article, ArticlesRepository $articlesRepository): Response
    {
        $articlesRepository->remove($article, true);

        $this->addFlash('success', '<span class="me-2 fa fa-circle-check"></span>Votre article a été supprimmé avec succès');

        return $this->redirectToRoute('app_admin_blog_index', [], Response::HTTP_SEE_OTHER);
    }

}
