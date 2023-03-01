<?php

namespace App\Controller\Admin;

use App\Entity\Categories;
use App\Form\Blog\Admin\CategoriesType;
use App\Repository\CategoriesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/blog/categories')]
class CategoriesController extends AbstractController
{
    #[Route('/', name: 'app_admin_categories_blog_index')]
    public function index(CategoriesRepository $categoriesRepository): Response
    {
        return $this->render('admin/blog/categories/index.html.twig', [
            'categories' => $categoriesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_categories_blog_new', methods: ['GET', 'POST'])]
    public function categoriesNew(Request $request, CategoriesRepository $categoriesRepository): Response
    {
        $categorie = new Categories();
        $form = $this->createForm(CategoriesType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoriesRepository->save($categorie, true);

            $this->addFlash('success', '<span class="me-2 fa fa-circle-check"></span>Votre catégorie a été enregistré avec succès');

            return $this->redirectToRoute('app_admin_categories_blog_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/blog/categories/edit.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_categories_blog_edit', methods: ['GET', 'POST'])]
    public function CategoriesEdit(Request $request, Categories $categorie, CategoriesRepository $categoriesRepository): Response
    {
        $form = $this->createForm(CategoriesType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoriesRepository->save($categorie, true);

            $this->addFlash('success', '<span class="me-2 fa fa-circle-check"></span>Votre catégorie a été enregistré avec succès');

            return $this->redirectToRoute('app_admin_categories_blog_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/blog/categories/edit.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_admin_categories_blog_delete', methods: ['GET'])]
    public function CategoriesDelete(Request $request, Categories $categorie, CategoriesRepository $categoriesRepository): Response
    {
        $categoriesRepository->remove($categorie, true);

        $this->addFlash('success', '<span class="me-2 fa fa-circle-check"></span>Votre catégorie a été supprimmé avec succès');

        return $this->redirectToRoute('app_admin_categories_blog_index', [], Response::HTTP_SEE_OTHER);
    }
}
