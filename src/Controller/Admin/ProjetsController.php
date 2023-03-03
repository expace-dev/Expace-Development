<?php

namespace App\Controller\Admin;

use App\Entity\Projets;
use App\Form\Admin\ProjetsType;
use App\Repository\ProjetsRepository;
use App\Services\MailerService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/projets')]
class ProjetsController extends AbstractController
{
    #[Route('/', name: 'app_admin_projets_index', methods: ['GET'])]
    public function index(ProjetsRepository $projetsRepository): Response
    {
        return $this->render('admin/projets/index.html.twig', [
            'projets' => $projetsRepository->findAll(),
        ]);
    }

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

    #[Route('/{id}/edit', name: 'app_admin_projets_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Projets $projet, ProjetsRepository $projetsRepository, MailerService $mailer): Response
    {
        $form = $this->createForm(ProjetsType::class, $projet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            

            $propositionCommercial = $form->get('propositionCommercial')->getData();
            $titre = str_replace(" ", "-", $projet->getTitre());
            
            if ($propositionCommercial) {
                
                $fichier = 'propositionCommerciale-' . $titre . '.' . $propositionCommercial->guessExtension();
                $propositionCommercial->move(
                    $this->getParameter('clients_directory'),
                    $fichier
                );
                $projet->setPropositionCommercial($fichier);

                $mailer->sendDocument(
                    from: 'noreply@expace-development.fr',
                    name: 'Expace Development',
                    to: $projet->getClient()->getEmail(),
                    template: 'emails/_new_doc.html.twig',
                    subject: 'Nouveau document',
                    attache: $this->getParameter('clients_directory') . '/' . $fichier,
                    mime: 'application/pdf',
                    docAttache: 'Proposition-commerciale.pdf',
                    client: $projet->getClient()->getPrenom(),
                    document: 'proposition commerciale'
                );


            }
            

            $cahierCharge = $form->get('cahierCharge')->getData();
            if ($cahierCharge) {
                $fichier = 'cahierCharge-' . $titre . '.' . $cahierCharge->guessExtension();
                $cahierCharge->move(
                    $this->getParameter('clients_directory'),
                    $fichier
                );
                $projet->setCahierCharge($fichier);

                $mailer->sendDocument(
                    from: 'noreply@expace-development.fr',
                    name: 'Expace Development',
                    to: 'mega-services@hotmail.fr',
                    //to: $projet->getClient()->getEmail(),
                    template: 'emails/_new_doc.html.twig',
                    subject: 'Nouveau document',
                    attache: $this->getParameter('clients_directory') . '/' . $fichier,
                    mime: 'application/pdf',
                    docAttache: 'Cahier-des-charges.pdf',
                    client: $projet->getClient()->getPrenom(),
                    document: 'cahier des charges'
                );
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

    #[Route('/{id}/delete', name: 'app_admin_projets_delete', methods: ['GET'])]
    public function delete(Request $request, Projets $projet, ProjetsRepository $projetsRepository): Response
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

    #[Route('/{id}/update-statut', name: 'app_admin_projets_update', methods: ['GET'])]
    public function update(Request $request, Projets $projet, ProjetsRepository $projetsRepository): Response
    {

          $projet->setStatut($request->query->get('statut'));
          $projetsRepository->save($projet, true);

          $this->addFlash('success', '<span class="me-2 fa fa-circle-check"></span>Le projet a été modifié avec succès');

        return $this->redirectToRoute('app_admin_projets_index', [], Response::HTTP_SEE_OTHER);
    }
}
