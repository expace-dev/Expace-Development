<?php

namespace App\Controller\Admin;

use DateTime;
use App\Entity\Projets;
use App\Entity\Notifications;
use App\Form\Admin\ProjetsType;
use App\Repository\NotificationsRepository;
use App\Services\MailerService;
use App\Repository\ProjetsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/projets')]
class ProjetsController extends AbstractController
{
    /**
     * Permet de lister les projets
     *
     * @return Response
     */
    #[Route('/', name: 'app_admin_projets_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/projets/index.html.twig');
    }

    /**
     * Permet de créer un projet
     *
     * @param Request $request
     * @param ProjetsRepository $projetsRepository
     * @return Response
     */
    #[Route('/new', name: 'app_admin_projets_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProjetsRepository $projetsRepository): Response
    {
        $projet = new Projets();
        $form = $this->createForm(ProjetsType::class, $projet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $projet->setCreatedAt(new DateTime());
            $projet->setStatut('ouverture');

            $projetsRepository->save($projet, true);

            $this->addFlash('success', '<span class="me-2 fa fa-circle-check"></span>Le projet a été enregistré avec succès');

            return $this->redirectToRoute('app_admin_projets_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/projets/edit.html.twig', [
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
     * @param MailerService $mailer
     * @param NotificationsRepository $notificationsRepository
     * @return Response
     */
    #[Route('/{id}/edit', name: 'app_admin_projets_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, 
        Projets $projet, 
        ProjetsRepository $projetsRepository, 
        MailerService $mailer, 
        NotificationsRepository $notificationsRepository,
    ): Response
    {
        $form = $this->createForm(ProjetsType::class, $projet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $propositionCommercial = $form->get('propositionCommercial')->getData();
            $titre = str_replace(" ", "-", $projet->getTitre());
            
            if ($propositionCommercial) {
                
                $fichier = 'propositionCommerciale-' .$titre  .$propositionCommercial->guessExtension();
                $propositionCommercial->move(
                    $this->getParameter('propositions_commerciales_directory'),
                    $fichier
                );
                $projet->setPropositionCommercial('documents/propositions_commerciales/' .$fichier);


                $mailer->sendDocument(
                    from: 'noreply@expace-development.fr',
                    name: 'Expace Development',
                    to: $projet->getClient()->getEmail(),
                    template: 'emails/_new_doc.html.twig',
                    subject: 'Nouveau document',
                    attache: $this->getParameter('propositions_commerciales_directory') . '/' . $fichier,
                    mime: 'application/pdf',
                    docAttache: 'Proposition-commerciale.pdf',
                    client: $projet->getClient()->getPrenom(),
                    document: 'proposition commerciale'
                );

                $notification = new Notifications();


                $notification->setSender($this->getUser())
                            ->setRecipient($projet->getClient())
                            ->setMessage('Réception d\'une proposition commerciale')
                            ->setDocument('teste')
                            ->setCreatedAt(new DateTime());

                $notificationsRepository->save($notification, true);


            }
            

            $cahierCharge = $form->get('cahierCharge')->getData();
            if ($cahierCharge) {
                $fichier = 'cahierCharge-' .$titre .$cahierCharge->guessExtension();
                $cahierCharge->move(
                    $this->getParameter('cahiers_charges_directory'),
                    $fichier
                );
                $projet->setCahierCharge('documents/cahiers_charges/' .$fichier);

                $mailer->sendDocument(
                    from: 'noreply@expace-development.fr',
                    name: 'Expace Development',
                    to: $projet->getClient()->getEmail(),
                    template: 'emails/_new_doc.html.twig',
                    subject: 'Nouveau document',
                    attache: $this->getParameter('cahiers_charges_directory') . '/' . $fichier,
                    mime: 'application/pdf',
                    docAttache: 'Cahier-des-charges.pdf',
                    client: $projet->getClient()->getPrenom(),
                    document: 'cahier des charges'
                );

                $notification = new Notifications();


                $notification->setSender($this->getUser())
                            ->setRecipient($projet->getClient())
                            ->setMessage('Réception d\'un cahier des charges')
                            ->setDocument('teste')
                            ->setCreatedAt(new DateTime());

                $notificationsRepository->save($notification, true);
            }
            
            

            $projetsRepository->save($projet, true);

            $this->addFlash('success', '<span class="me-2 fa fa-circle-check"></span>Le projet a été enregistré avec succès');

            return $this->redirectToRoute('app_admin_projets_index', [], Response::HTTP_SEE_OTHER);
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('danger', '<span class="me-2 fa fa-circle-exclamation"></span> Des erreurs subsistent, veuillez modifier votre saisie');
        }

        return $this->render('admin/projets/edit.html.twig', [
            'projet' => $projet,
            'form' => $form,
        ]);
    }

    /**
     * Permet de supprimer un projet
     *
     * @param Request $request
     * @param Projets $projet
     * @param ProjetsRepository $projetsRepository
     * @return Response
     */
    #[Route('/{id}/delete', name: 'app_admin_projets_delete', methods: ['GET'])]
    public function delete(Projets $projet, ProjetsRepository $projetsRepository): Response
    {
        if ($projet->getStatut() === 'ouverture') {

            $this->addFlash('success', '<span class="me-2 fa fa-circle-check"></span>Le projet a été supprimé avec succès');

            $projetsRepository->remove($projet, true);
        }
        else {
            $this->addFlash('danger', '<span class="me-2 fa fa-circle-exclamation"></span>Ce projet ne peut plus être supprimmé');
        }
        

        return $this->redirectToRoute('app_admin_projets_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Permet de modifier le statut d'un projet
     *
     * @param Request $request
     * @param Projets $projet
     * @param ProjetsRepository $projetsRepository
     * @return Response
     */
    #[Route('/{id}/update-statut', name: 'app_admin_projets_update', methods: ['GET'])]
    public function update(Request $request, Projets $projet, ProjetsRepository $projetsRepository): Response
    {

          $projet->setStatut($request->query->get('statut'));
          $projetsRepository->save($projet, true);

          $this->addFlash('success', '<span class="me-2 fa fa-circle-check"></span>Le projet a été modifié avec succès');

        return $this->redirectToRoute('app_admin_projets_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Permet de voir la proposition commerciale d'un projet
     *
     * @param Projets $projet
     * @return void
     */
    #[Route('/voir/proposition-commerciale/{slug}', name: 'app_admin_projets_voir_pc', methods: ['GET'])]
    public function viewPropositionCommerciale(Projets $projet): void
    {

        $mime = "application/pdf";
        $fichier = $projet->getPropositionCommercial();


            header('Content-type: ' . $mime);
            readfile($fichier);
    }

    /**
     * Permet de voir le cahier des charges d'un projet
     *
     * @param Projets $projet
     * @return void
     */
    #[Route('/voir/cahier-des-charges/{slug}', name: 'app_admin_projets_voir_cdc', methods: ['GET'])]
    public function viewCahierDesCharges(Projets $projet)
    {

        $mime = "application/pdf";
        $fichier = $projet->getCahierCharge();


            header('Content-type: ' . $mime);
            readfile($fichier);
    }
}
