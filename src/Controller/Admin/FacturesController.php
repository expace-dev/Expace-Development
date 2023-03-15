<?php

namespace App\Controller\Admin;

use App\Entity\Factures;
use App\Form\Admin\FacturesType;
use App\Services\InvoiceService;
use App\Services\NumInvoiceService;
use App\Repository\FacturesRepository;
use App\Services\NotificationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/factures')]
class FacturesController extends AbstractController
{
    /**
     * 
     *
     * @param FacturesRepository $facturesRepository
     * @param NumInvoiceService $numInvoiceService
     * @param InvoiceService $invoiceService
     * @param NotificationService $notificationService
     */
    public function __construct(
        private FacturesRepository $facturesRepository, 
        private NumInvoiceService $numInvoiceService, 
        private InvoiceService $invoiceService, 
        private NotificationService $notificationService,
    )
    {
        
    }
    /**
     * Permet de lister les factures
     *
     * @return Response
     */
    #[Route('/', name: 'app_admin_factures_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/factures/index.html.twig');
    }

    /**
     * Permet de créer une facture
     *
     * @return Response
     */
    #[Route('/new', name: 'app_admin_factures_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $facture = new Factures();
        $form = $this->createForm(FacturesType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $numero = $this->numInvoiceService->Generate(
                numInvoice: $this->facturesRepository->count([])+1,
                type: 'FACTURE'
            );

            
            $url = 'documents/factures/' . $numero . '.pdf';
            
            $facture->setClient($facture->getProjet()->getClient());
            $slug = $numero . '.pdf';
            $facture->setSlug($slug);

            $this->invoiceService->CreateDevis(
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

            $this->facturesRepository->save($facture, true);

            $this->notificationService->addNotification(
                sender: $this->getUser(),
                recipient: $facture->getClient(),
                message: 'Votre facture' .' ' .$numero .' ' .'a été créé',
                document: $facture->getSlug(),
                type: 'facture'
            );

            $this->addFlash('success', '<span class="me-2 fa fa-circle-check"></span>La facture a été enregistré avec succès');

            return $this->redirectToRoute('app_admin_factures_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/factures/edit.html.twig', [
            'facture' => $facture,
            'form' => $form,
        ]);
    }

    /**
     * Permet d'afficher une facture
     * 
     * @return void
     */
    #[Route('/{slug}', name: 'app_admin_factures_show', methods: ['GET'])]
    public function show(Factures $facture): void
    {

        $mime = "application/pdf";
        $fichier = $facture->getUrl();

            header('Content-type: ' . $mime);
            readfile($fichier);
    }

    /**
     * Permet d'éditer une facture
     *
     * @return Response
     */
    #[Route('/{id}/edit', name: 'app_admin_factures_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Factures $facture): Response
    {
        $form = $this->createForm(FacturesType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            
            $url = 'documents/factures/' . $facture->getSlug();
            

            $this->invoiceService->CreateDevis(
                numero: $facture->getSlug(),
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

            $this->facturesRepository->save($facture, true);

            
            $numero = substr($facture->getSlug(), 0, -4);

            $this->notificationService->addNotification(
                sender: $this->getUser(),
                recipient: $facture->getClient(),
                message: 'Votre facture' .' ' .$numero .' ' .'a été modifié',
                document: $facture->getSlug(),
                type: 'facture'
            );

            $this->addFlash('success', '<span class="me-2 fa fa-circle-check"></span>La facture a été enregistré avec succès');

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
