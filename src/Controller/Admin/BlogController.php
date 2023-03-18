<?php

namespace App\Controller\Admin;

use App\Entity\Articles;
use App\Services\UploadService;
use App\Form\Blog\Admin\ArticlesType;
use App\Repository\ArticlesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/blog')]
class BlogController extends AbstractController
{
    /**
     * Permet de lister les articles de blog
     *
     * @return Response
     */
    #[Route('/', name: 'app_admin_blog_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/blog/index.html.twig');
    }

    /**
     * Permet de créer un nouvel article de blog
     *
     * @param Request $request
     * @param ArticlesRepository $articlesRepository
     * @param UploadService $uploadService
     * @return Response
     */
    #[Route('/new', name: 'app_admin_blog_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ArticlesRepository $articlesRepository, UploadService $uploadService): Response
    {
        $article = new Articles();
        $form = $this->createForm(ArticlesType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            // Si on réceptionne une image d'illustration
            if ($form->get('img')->getData()) {
                // On récupère l'image
                $fichier = $form->get('img')->getData();
                // On récupère le dossier de destination
                $directory = 'blog_directory';
                /**
                * On ajoute l'image et l'utilisateur connecté à l'article
                * et ont upload l'image via UploadService
                */
                $article->setImg('/images/blog/' .$uploadService->send($fichier, $directory))
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
        if (!$this->getUser()->getDescription()) {

            $this->addFlash('danger', '<span class="me-2 fa fa-circle-exclamation"></span>Vous devez configurer votre profil avant de publier un article');
            return $this->redirectToRoute('app_profil_edit', [], Response::HTTP_SEE_OTHER);
        }



        return $this->render('admin/blog/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    /**
     * Permet d'éditer un article de blog
     *
     * @param Request $request
     * @param Articles $article
     * @param ArticlesRepository $articlesRepository
     * @param UploadService $uploadService
     * @return Response
     */
    #[Route('/{id}/edit', name: 'app_admin_blog_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Articles $article, ArticlesRepository $articlesRepository, UploadService $uploadService): Response
    {
        $form = $this->createForm(ArticlesType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Si on réceptionne une image d'illustration
            if ($form->get('img')->getData()) {
                // On récupère l'image
                $fichier = $form->get('img')->getData();
                // On récupère le répertoire de destination
                $directory = 'blog_directory';
                // On supprime l'ancienne image d'illustration
                unlink($article->getImg());
                // Puis on upload la nouvelle image et on ajoute cela à  l'article
                $article->setImg('/images/blog/' .$uploadService->send($fichier, $directory));
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

    /**
     * Permet de supprimer un article de blog
     * 
     * @param Articles $article
     * @param ArticlesRepository $articlesRepository
     * @return Response
     */
    #[Route('/delete/{id}', name: 'app_admin_blog_delete', methods: ['GET'])]
    public function delete(Articles $article, ArticlesRepository $articlesRepository): Response
    {
        $articlesRepository->remove($article, true);

        $this->addFlash('success', '<span class="me-2 fa fa-circle-check"></span>Votre article a été supprimmé avec succès');

        return $this->redirectToRoute('app_admin_blog_index', [], Response::HTTP_SEE_OTHER);
    }

}
