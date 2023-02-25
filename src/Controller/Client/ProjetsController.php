<?php

namespace App\Controller\Client;

use DateTime;
use App\Entity\Projets;
use App\Form\Client\ProjetsType;
use App\Repository\ProjetsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/client/projets')]
class ProjetsController extends AbstractController
{
    #[Route('/', name: 'app_client_projets_index', methods: ['GET'])]
    public function index(ProjetsRepository $projetsRepository): Response
    {
        return $this->render('client/projets/index.html.twig', [
            'projets' => $projetsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_client_projets_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProjetsRepository $projetsRepository): Response
    {
        $projet = new Projets();
        $form = $this->createForm(ProjetsType::class, $projet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $projet->setCreatedAt(new DateTime());
            $projet->setStatut('ouverture');
            $projet->setClient($this->getUser());

            $projetsRepository->save($projet, true);

            $this->addFlash('success', '<span class="me-2 fa fa-circle-check"></span>Votre projet a été enregistré avec succès');

            return $this->redirectToRoute('app_client_projets_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('client/projets/edit.html.twig', [
            'projet' => $projet,
            'form' => $form,
        ]);
    }

    #[Route('/edit/{slug}', name: 'app_client_projets_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Projets $projet, ProjetsRepository $projetsRepository): Response
    {
        if ($projet->getClient() !== $this->getUser()) {
            throw new AccessDeniedException("Vous n'avez pas l'autorisation d'accéder à cette page");
        }
        $form = $this->createForm(ProjetsType::class, $projet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $projetsRepository->save($projet, true);

            $this->addFlash('success', '<span class="me-2 fa fa-circle-check"></span>Votre projet a été enregistré avec succès');

            return $this->redirectToRoute('app_client_projets_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('client/projets/edit.html.twig', [
            'projet' => $projet,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}/delete', name: 'app_client_projets_delete', methods: ['GET'])]
    public function delete(Request $request, Projets $projet, ProjetsRepository $projetsRepository): Response
    {
        if ($projet->getClient() !== $this->getUser()) {
            throw new AccessDeniedException("Vous n'avez pas l'autorisation d'accéder à cette page");
        }
        if ($projet->getStatut() !== 'ouverture') {
            $this->addFlash('danger', '<span class="me-2 fa fa-circle-exclamation"></span>Vous ne pouvez plus supprimmer ce projet');
        }
        else {
            $this->addFlash('success', '<span class="me-2 fa fa-circle-check"></span>Votre projet a été supprimmé avec succès');
            $projetsRepository->remove($projet, true);
        }
        

        return $this->redirectToRoute('app_client_projets_index', [], Response::HTTP_SEE_OTHER);
    }
}
