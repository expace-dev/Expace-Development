<?php

namespace App\Controller\Admin;

use App\Entity\Devis;
use App\Form\DevisType;
use App\Repository\DevisRepository;
use App\Services\InvoiceService;
use App\Services\MailerService;
use App\Services\NumInvoiceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/devis')]
class DevisController extends AbstractController
{
    #[Route('/', name: 'app_admin_devis_index', methods: ['GET'])]
    public function index(DevisRepository $devisRepository): Response
    {
        return $this->render('admin/devis/index.html.twig', [
            'devis' => $devisRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_devis_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DevisRepository $devisRepository, InvoiceService $invoiceService, NumInvoiceService $numInvoiceService, MailerService $mailer): Response
    {
        $devis = new Devis();
        $form = $this->createForm(DevisType::class, $devis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            
            $numero = $numInvoiceService->Generate(
                numInvoice: $devisRepository->count([])+1,
                type: 'DEVIS'
            );

            
            $url = 'documents_clients/' . $numero . '.pdf';
            
            $devis->setClient($devis->getProjet()->getClient());
            $slug = $numero . '.pdf';
            $devis->setSlug($slug);

            $invoiceService->CreateDevis(
                numero: $numero,
                url: $url,
                type: 'DEVIS',
                document: $devis
            );
            
            $devis->setUrl($url);

            $tarif_total = null;
            foreach ($devis->getServices() as $values) {
                $tarif_total += $values['tarif']*$values['quantite'];
            }

            $devis->setAmount($tarif_total);
            


            $devisRepository->save($devis, true);

            $mailer->sendDocument(
                from: 'noreply@expace-development.fr',
                name: 'Expace Development',
                to: $devis->getClient()->getEmail(),
                template: 'emails/_new_doc.html.twig',
                subject: 'Nouveau document',
                attache: $this->getParameter('clients_directory') . '/' . $slug,
                mime: 'application/pdf',
                docAttache: $slug .'.pdf',
                client: $devis->getClient()->getPrenom(),
                document: 'Devis'
            );
        
            $this->addFlash('success', '<span class="me-2 fa fa-circle-check"></span>Le devis a été enregistré avec succès');

            return $this->redirectToRoute('app_admin_devis_index', [], Response::HTTP_SEE_OTHER);
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('danger', '<span class="me-2 fa fa-circle-exclamation"></span> Des erreurs subsistent, veuillez modifier votre saisie');
        }

        return $this->render('admin/devis/edit.html.twig', [
            'devi' => $devis,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'app_admin_devis_show', methods: ['GET'])]
    public function show(Devis $devis)
    {

        $mime = "application/pdf";
        $fichier = $devis->getUrl();

            header('Content-type: ' . $mime);
            readfile($fichier);
    }


    #[Route('/{id}/edit', name: 'app_admin_devis_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Devis $devis, DevisRepository $devisRepository, NumInvoiceService $numInvoiceService, InvoiceService $invoiceService, MailerService $mailer): Response
    {
        $form = $this->createForm(DevisType::class, $devis);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {


                       
            $url = 'documents_clients/' . $devis->getSlug();
            $devis->setClient($devis->getProjet()->getClient());
            
            $invoiceService->CreateDevis(
                numero: $devis->getSlug(),
                url: $url,
                type: 'DEVIS',
                document: $devis
            );
            
            $devis->setUrl($url);

            $tarif_total = null;
            foreach ($devis->getServices() as $values) {
                $tarif_total += $values['tarif']*$values['quantite'];
            }

            $devis->setAmount($tarif_total);


            $devisRepository->save($devis, true);

            $mailer->sendDocument(
                from: 'noreply@expace-development.fr',
                name: 'Expace Development',
                to: $devis->getClient()->getEmail(),
                template: 'emails/_new_doc.html.twig',
                subject: 'Nouveau document',
                attache: $this->getParameter('clients_directory') . '/' . $devis->getSlug(),
                mime: 'application/pdf',
                docAttache: $devis->getSlug(),
                client: $devis->getClient()->getPrenom(),
                document: 'Devis'
            );

            $this->addFlash('success', '<span class="me-2 fa fa-circle-check"></span>Le devis a été enregistré avec succès');

            return $this->redirectToRoute('app_admin_devis_index', [], Response::HTTP_SEE_OTHER);
        }

        if ($devis->getStatut() !== 'en_attente') {

            $this->addFlash('danger', '<span class="me-2 fa fa-circle-exclamation"></span>Vous ne pouvez plus modifier ce devis');
            return $this->redirectToRoute('app_admin_devis_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/devis/edit.html.twig', [
            'devi' => $devis,
            'form' => $form,
        ]);
    }

}
