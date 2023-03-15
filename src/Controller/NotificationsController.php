<?php

namespace App\Controller;

use App\Entity\Notifications;
use App\Form\NotificationsType;
use App\Repository\DevisRepository;
use App\Repository\ProjetsRepository;
use App\Repository\NotificationsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/profile/notifications')]
class NotificationsController extends AbstractController
{
    /**
     * Permet de lister les notifications
     *
     * @return Response
     */
    #[Route('/', name: 'app_notifications_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('notifications/index.html.twig');
    }

    /**
     * Permet de voir le document relatif à la notification
     * 
     * @param Notifications $notification
     * @param ProjetsRepository $projetsRepository
     * @param NotificationsRepository $notificationsRepository
     * @return Response
     */
    #[Route('/{id}', name: 'app_notifications_show', methods: ['GET'])]
    public function show(
        Notifications $notification, 
        ProjetsRepository $projetsRepository, 
        NotificationsRepository $notificationsRepository,
        DevisRepository $devisRepository
    ): Response
    {
        if ($notification->getRecipient() !== $this->getUser()) {
            throw new AccessDeniedException("Vous n'avez pas l'autorisation d'accéder à cette page");
        }
        else {
            $notification->setLu(true);
            $notificationsRepository->save($notification, true);
        }

        if ($notification->getType() === 'projet') {
            
            $projet = $projetsRepository->findOneBy(['slug' => $notification->getDocument()]);
            
            return $this->redirectToRoute('app_admin_projets_edit', ['id' => $projet->getId()], Response::HTTP_SEE_OTHER);

        }

        if ($notification->getType() === 'devis') {
            
            $devis = $devisRepository->findOneBy(['slug' => $notification->getDocument()]);
            return $this->redirectToRoute('app_client_devis_show', ['slug' => $devis->getSlug()], Response::HTTP_SEE_OTHER);

        }

        if ($notification->getType() === 'proposition_commenciale') {
            
            $projet = $projetsRepository->findOneBy(['slug' => $notification->getDocument()]);
            return $this->redirectToRoute('app_client_projets_voir_pc', ['slug' => $projet->getSlug()], Response::HTTP_SEE_OTHER);

        }

        if ($notification->getType() === 'cahier_charges') {
            
            $projet = $projetsRepository->findOneBy(['slug' => $notification->getDocument()]);
            return $this->redirectToRoute('app_client_projets_voir_cdc', ['slug' => $projet->getSlug()], Response::HTTP_SEE_OTHER);

        }
       
        

    }

    /**
     * Permet de supprimer une notification
     * 
     * @param Notifications $notification
     * @param NotificationsRepository $notificationsRepository
     * @return Response
     */
    #[Route('/delete/{id}', name: 'app_notifications_delete', methods: ['GET'])]
    public function delete(Notifications $notification, NotificationsRepository $notificationsRepository): Response
    {
        if ($notification->getRecipient() !== $this->getUser()) {
            throw new AccessDeniedException("Vous n'avez pas l'autorisation d'accéder à cette page");
        }
        else {
            $notificationsRepository->remove($notification, true);

            $this->addFlash('success', '<span class="me-2 fa fa-circle-check"></span>Votre notification a été supprimé avec succès');
        }
        

        return $this->redirectToRoute('app_notifications_index', [], Response::HTTP_SEE_OTHER);
    }
}
