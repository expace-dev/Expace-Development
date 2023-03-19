<?php

namespace App\Controller\Client;

use DateTime;
use App\Entity\Projets;
use Cocur\Slugify\Slugify;
use App\Entity\Notifications;
use App\Form\Client\ProjetsType;
use App\Repository\UsersRepository;
use App\Repository\ProjetsRepository;
use App\Repository\NotificationsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/panel/projets')]
class ProjetsController extends AbstractController
{
    /**
     * Permet de lister les projets d'un client
     *
     * @return Response
     */
    #[Route('/', name: 'app_client_projets_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('client/projets/index.html.twig');
    }

    /**
     * Permet de créer un projet
     *
     * @param Request $request
     * @param ProjetsRepository $projetsRepository
     * @param NotificationsRepository $notificationsRepository
     * @param UsersRepository $usersRepository
     * @return Response
     */
    #[Route('/new', name: 'app_client_projets_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        ProjetsRepository $projetsRepository, 
        NotificationsRepository $notificationsRepository, 
        UsersRepository $usersRepository
    ): Response
    {
        $projet = new Projets();
        $form = $this->createForm(ProjetsType::class, $projet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $projet->setCreatedAt(new DateTime());
            $projet->setStatut('ouverture');
            $projet->setClient($this->getUser());

            $slugify = new Slugify();


            $projetsRepository->save($projet, true);

            $users = $usersRepository->findAllUsers('["ROLE_ADMIN"]');


            foreach ($users as $user) {
                $notification = new Notifications();


                $notification->setSender($this->getUser())
                            ->setRecipient($user)
                            ->setMessage('Réception d\'un nouveau projet')
                            ->setDocument($slugify->slugify($projet->getTitre()))
                            ->setCreatedAt(new DateTime())
                            ->setType('projet');


                $notificationsRepository->save($notification, true);
            }

            $this->addFlash('success', '<span class="me-2 fa fa-circle-check"></span>Votre projet a été enregistré avec succès');

            return $this->redirectToRoute('app_client_projets_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('client/projets/edit.html.twig', [
            'projet' => $projet,
            'form' => $form,
        ]);
    }

    /**
     * Permet d'éditer un projet
     *
     * @param Request $request
     * @param Projets $projet
     * @param ProjetsRepository $projetsRepository
     * @return Response
     */
    #[Route('/edit/{slug}', name: 'app_client_projets_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Projets $projet, ProjetsRepository $projetsRepository): Response
    {
        if ($projet->getClient() !== $this->getUser()) {
            throw new AccessDeniedException("Vous n'avez pas l'autorisation d'accéder à cette page");
        }
        
        $form = $this->createForm(ProjetsType::class, $projet);
        $form->handleRequest($request);
        
        if ($projet->getStatut() !== 'ouverture') {
                $this->addFlash('danger', '<span class="me-2 fa fa-circle-exclamation"></span>Vous ne pouvez plus modifier ce projet');
                return $this->redirectToRoute('app_client_projets_index', [], Response::HTTP_SEE_OTHER);
            }

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

    /**
     * Permet de supprimer un projet
     *
     * @param Projets $projet
     * @param ProjetsRepository $projetsRepository
     * @return Response
     */
    #[Route('/{slug}/delete', name: 'app_client_projets_delete', methods: ['GET'])]
    public function delete(Projets $projet, ProjetsRepository $projetsRepository, NotificationsRepository $notificationsRepository): Response
    {
        if ($projet->getClient() !== $this->getUser()) {
            throw new AccessDeniedException("Vous n'avez pas l'autorisation d'accéder à cette page");
        }
        if ($projet->getStatut() !== 'ouverture') {
            $this->addFlash('danger', '<span class="me-2 fa fa-circle-exclamation"></span>Vous ne pouvez plus supprimmer ce projet');
        }
        else {
            
            $notifications = $notificationsRepository->findBy(['document' => $projet->getSlug()]);

            foreach ($notifications as $notification) {
                $notificationsRepository->remove($notification, true);
            }
            
            $this->addFlash('success', '<span class="me-2 fa fa-circle-check"></span>Votre projet a été supprimmé avec succès');
            $projetsRepository->remove($projet, true);
        }
        

        return $this->redirectToRoute('app_client_projets_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Permet de voir la proposition commerciale
     *
     * @param Projets $projet
     * @return void
     */
    #[Route('/voir/proposition-commerciale/{slug}', name: 'app_client_projets_voir_pc', methods: ['GET'])]
    public function viewPropositionCommerciale(Projets $projet): void
    {
        if ($projet->getClient() !== $this->getUser()) {
            throw new AccessDeniedException("Vous n'avez pas l'autorisation d'accéder à cette page");
        }

        $mime = "application/pdf";
        $fichier = $projet->getPropositionCommercial();


            header('Content-type: ' . $mime);
            readfile($fichier);
    }

    /**
     * Permet de voir le cahier des charges
     *
     * @param Projets $projet
     * @return void
     */
    #[Route('/voir/cahier-des-charges/{slug}', name: 'app_client_projets_voir_cdc', methods: ['GET'])]
    public function viewCahierDesCharges(Projets $projet): void
    {
        if ($projet->getClient() !== $this->getUser()) {
            throw new AccessDeniedException("Vous n'avez pas l'autorisation d'accéder à cette page");
        }

        $mime = "application/pdf";
        $fichier = $projet->getCahierCharge();


            header('Content-type: ' . $mime);
            readfile($fichier);
    }
}
