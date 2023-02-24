<?php

namespace App\Controller\Admin;

use App\Entity\Portfolios;
use App\Form\PortfoliosType;
use App\Services\UploadService;
use App\Entity\ImagesPortfolios;
use App\Repository\ImagesPortfoliosRepository;
use App\Repository\PortfoliosRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/portfolios')]
class PortfoliosController extends AbstractController
{
    #[Route('/', name: 'app_admin_portfolios_index', methods: ['GET'])]
    public function index(PortfoliosRepository $portfoliosRepository): Response
    {
        return $this->render('admin/portfolios/index.html.twig', [
            'portfolios' => $portfoliosRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_portfolios_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PortfoliosRepository $portfoliosRepository, UploadService $uploadService): Response
    {
        $portfolio = new Portfolios();
        $form = $this->createForm(PortfoliosType::class, $portfolio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $fichiers = $form->get('imagesPortfolios')->getData();

            foreach ($fichiers as $fichier) {

                $img = new ImagesPortfolios();
                $img->setUrl('images/portfolios/' .$uploadService->send($fichier));
                $portfolio->addImagesPortfolio($img);
                
            }

            $portfoliosRepository->save($portfolio, true);

            return $this->redirectToRoute('app_admin_portfolios_index', [], Response::HTTP_SEE_OTHER);
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('danger', '<span class="me-2 fa fa-circle-exclamation"></span> Des erreurs subsistent, veuillez modifier votre saisie');
        }

        return $this->render('admin/portfolios/edit.html.twig', [
            'portfolio' => $portfolio,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_portfolios_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Portfolios $portfolio, PortfoliosRepository $portfoliosRepository, UploadService $uploadService): Response
    {
        $form = $this->createForm(PortfoliosType::class, $portfolio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $fichiers = $form->get('imagesPortfolios')->getData();

            foreach ($fichiers as $fichier) {

                $img = new ImagesPortfolios();
                $img->setUrl('images/portfolios/' .$uploadService->send($fichier));
                $portfolio->addImagesPortfolio($img);
                
            }


            $portfoliosRepository->save($portfolio, true);

            $this->addFlash('success', '<span class="me-2 fa fa-circle-check"></span>Le projet a été enregistré avec succès');

            return $this->redirectToRoute('app_admin_portfolios_index', [], Response::HTTP_SEE_OTHER);
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('danger', '<span class="me-2 fa fa-circle-exclamation"></span> Des erreurs subsistent, veuillez modifier votre saisie');
        }

        return $this->render('admin/portfolios/edit.html.twig', [
            'portfolio' => $portfolio,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_portfolios_delete', methods: ['GET'])]
    public function delete(Request $request, Portfolios $portfolio, PortfoliosRepository $portfoliosRepository): Response
    {
        $portfoliosRepository->remove($portfolio, true);

        $this->addFlash('success', '<span class="me-2 fa fa-circle-check"></span>Le projet a été supprimmé avec succès');

        return $this->redirectToRoute('app_admin_portfolios_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/image/supprime/{id}', name: 'app_admin_portfolios_image_delete', methods: ['DELETE'])]
    public function deleteImage(Request $request, ImagesPortfolios $image, ImagesPortfoliosRepository $imagesPortfoliosRepository) {

        $data = json_decode($request->getContent(), true);

        if($this->isCsrfTokenValid('delete'.$image->getId(), $data['_token'])) {
            

            unlink($image->getUrl());
            $imagesPortfoliosRepository->remove($image, true);

            return new JsonResponse(['success' => 1]);
        }
        else {
            return new JsonResponse(['error' => 'Token invalide'], 400);
        }

    }
}
