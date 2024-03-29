<?php

namespace App\Controller\Admin;

use App\Entity\Portfolios;
use App\Services\UploadService;
use App\Entity\ImagesPortfolios;
use App\Form\Admin\PortfoliosCreateType;
use App\Form\Admin\PortfoliosEditType;
use App\Repository\PortfoliosRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ImagesPortfoliosRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/portfolios')]
class PortfoliosController extends AbstractController
{
    /**
     * Permet de lister les réalisations
     *
     * @return Response
     */
    #[Route('/', name: 'app_admin_portfolios_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/portfolios/index.html.twig');
    }

    /**
     * Permet de créer une réalisation
     *
     * @param Request $request
     * @param PortfoliosRepository $portfoliosRepository
     * @param UploadService $uploadService
     * @return Response
     */
    #[Route('/new', name: 'app_admin_portfolios_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PortfoliosRepository $portfoliosRepository, UploadService $uploadService): Response
    {
        $portfolio = new Portfolios();
        $form = $this->createForm(PortfoliosCreateType::class, $portfolio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $portfolio->setSlug($portfolio->getProjet()->getSlug());


            $fichiers = $form->get('imagesPortfolios')->getData();
            $directory = 'portfolios_directory';

            foreach ($fichiers as $fichier) {

                $img = new ImagesPortfolios();
                $img->setUrl('images/portfolios/' .$uploadService->send($fichier, $directory));
                $portfolio->addImagesPortfolio($img);
                
            }

            $portfoliosRepository->save($portfolio, true);

            return $this->redirectToRoute('app_admin_portfolios_index', [], Response::HTTP_SEE_OTHER);
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('danger', '<span class="me-2 fa fa-circle-exclamation"></span> Des erreurs subsistent, veuillez modifier votre saisie');
        }

        return $this->render('admin/portfolios/new.html.twig', [
            'portfolio' => $portfolio,
            'form' => $form,
        ]);
    }

    /**
     * Permet d'éditer une réalisation
     *
     * @param Request $request
     * @param Portfolios $portfolio
     * @param PortfoliosRepository $portfoliosRepository
     * @param UploadService $uploadService
     * @return Response
     */
    #[Route('/{id}/edit', name: 'app_admin_portfolios_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, 
        Portfolios $portfolio, 
        PortfoliosRepository $portfoliosRepository, 
        UploadService $uploadService
    ): Response
    {
        $form = $this->createForm(PortfoliosEditType::class, $portfolio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $fichiers = $form->get('imagesPortfolios')->getData();
            $directory = 'portfolios_directory';

            foreach ($fichiers as $fichier) {

                $img = new ImagesPortfolios();
                $img->setUrl('images/portfolios/' .$uploadService->send($fichier, $directory));
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

    /**
     * Permet de supprimer une réalisation
     *
     * @param Portfolios $portfolio
     * @param PortfoliosRepository $portfoliosRepository
     * @return Response
     */
    #[Route('/{id}/delete', name: 'app_admin_portfolios_delete', methods: ['GET'])]
    public function delete(Portfolios $portfolio, PortfoliosRepository $portfoliosRepository): Response
    {
        $portfoliosRepository->remove($portfolio, true);

        $this->addFlash('success', '<span class="me-2 fa fa-circle-check"></span>Le projet a été supprimmé avec succès');

        return $this->redirectToRoute('app_admin_portfolios_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Permet de supprimer une image de la réalisation
     *
     * @param Request $request
     * @param ImagesPortfolios $image
     * @param ImagesPortfoliosRepository $imagesPortfoliosRepository
     * @return JsonResponse
     */
    #[Route('/image/supprime/{id}', name: 'app_admin_portfolios_image_delete', methods: ['DELETE'])]
    public function deleteImage(Request $request, ImagesPortfolios $image, ImagesPortfoliosRepository $imagesPortfoliosRepository) {

        $data = json_decode($request->getContent(), true);

        if($this->isCsrfTokenValid('delete'.$image->getId(), $data['_token'])) {
            

            unlink($image->getUrl());
            $imagesPortfoliosRepository->remove($image, true);

            return new JsonResponse(['success' => 1]);
        }
        else {
            return new JsonResponse(['error' => 'Token invalide'], 403);
        }

    }
}
