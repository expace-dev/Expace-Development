<?php

namespace App\Controller\Admin;

use App\Entity\Factures;
use App\Form\FacturesType;
use App\Repository\FacturesRepository;
use App\Services\InvoiceService;
use App\Services\NumInvoiceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/factures')]
class FacturesController extends AbstractController
{
    #[Route('/', name: 'app_admin_factures_index', methods: ['GET'])]
    public function index(FacturesRepository $facturesRepository): Response
    {
        return $this->render('admin/factures/index.html.twig', [
            'factures' => $facturesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_factures_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FacturesRepository $facturesRepository, NumInvoiceService $numInvoiceService, InvoiceService $invoiceService): Response
    {
        $facture = new Factures();
        $form = $this->createForm(FacturesType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $numero = $numInvoiceService->Generate(
                numInvoice: $facturesRepository->count([])+1,
                type: 'FACTURE'
            );

            
            $url = 'documents_clients/' . $numero . '.pdf';
            
            $facture->setClient($facture->getProjet()->getClient());
            $slug = $numero . '.pdf';
            $facture->setSlug($slug);

            $invoiceService->CreateDevis(
                numero: $numero,
                url: $url,
                type: 'FACTURE',
                document: $facture
            );
            
            $facture->setUrl($url);

            $tarif_total = null;
            foreach ($facture->getServices() as $values) {
                $tarif_total += $values['tarif']*$values['quantite'];
            }

            $facture->setAmount($tarif_total);

            $facturesRepository->save($facture, true);

            return $this->redirectToRoute('app_admin_factures_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/factures/new.html.twig', [
            'facture' => $facture,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'app_admin_factures_show', methods: ['GET'])]
    public function show(Factures $facture)
    {

        $mime = "application/pdf";
        $fichier = $facture->getUrl();

            header('Content-type: ' . $mime);
            readfile($fichier);
    }


    #[Route('/{id}/edit', name: 'app_admin_factures_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Factures $facture, FacturesRepository $facturesRepository, NumInvoiceService $numInvoiceService, InvoiceService $invoiceService): Response
    {
        $form = $this->createForm(FacturesType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $numero = $numInvoiceService->Generate(
                numInvoice: $facturesRepository->count([])+1,
                type: 'FACTURE'
            );

            
            $url = 'documents_clients/' . $numero . '.pdf';
            
            $facture->setClient($facture->getProjet()->getClient());
            $slug = $numero . '.pdf';
            $facture->setSlug($slug);

            $invoiceService->CreateDevis(
                numero: $numero,
                url: $url,
                type: 'FACTURE',
                document: $facture
            );
            
            $facture->setUrl($url);

            $tarif_total = null;
            foreach ($facture->getServices() as $values) {
                $tarif_total += $values['tarif']*$values['quantite'];
            }

            $facture->setAmount($tarif_total);

            $facturesRepository->save($facture, true);

            return $this->redirectToRoute('app_admin_factures_index', [], Response::HTTP_SEE_OTHER);
        }

        if ($facture->getStatut() !== 'en_attente') {

            $this->addFlash('danger', '<span class="me-2 fa fa-circle-exclamation"></span>Vous ne pouvez plus modifier cette facture');
            return $this->redirectToRoute('app_admin_factures_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/factures/edit.html.twig', [
            'facture' => $facture,
            'form' => $form,
        ]);
    }

}
