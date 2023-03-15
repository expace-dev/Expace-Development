<?php

namespace App\Controller\Admin;

use App\Entity\Devis;
use App\Form\Admin\DevisType;
use App\Services\InvoiceService;
use App\Repository\DevisRepository;
use App\Services\NumInvoiceService;
use App\Services\NotificationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/devis')]
class DevisController extends AbstractController
{

    /**
     * 
     *
     * @param DevisRepository $devisRepository
     * @param InvoiceService $invoiceService
     * @param NumInvoiceService $numInvoiceService
     * @param NotificationService $notificationService
     */
    public function __construct(
        private DevisRepository $devisRepository, 
        private InvoiceService $invoiceService, 
        private NumInvoiceService $numInvoiceService, 
        private NotificationService $notificationService,
    )
    {
    }


    /**
     * Permet de lister les devis client
     *
     * @return Response
     */
    #[Route('/', name: 'app_admin_devis_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/devis/index.html.twig');
    }

    /**
     * Permet de créer un nouveau devis
     *
     * @return Response
     */
    #[Route('/new', name: 'app_admin_devis_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $devis = new Devis();
        $form = $this->createForm(DevisType::class, $devis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // On génère un numéro de devis
            $numero = $this->numInvoiceService->Generate(
                numInvoice: $this->devisRepository->count([])+1,
                type: 'DEVIS'
            );

            
            $url = 'documents/devis/' . $numero . '.pdf';
            
            $devis->setClient($devis->getProjet()->getClient());
            $slug = $numero . '.pdf';
            $devis->setSlug($slug);

            // On génère un pdf
            $this->invoiceService->CreateDevis(
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
            $this->devisRepository->save($devis, true);

            $this->notificationService->addNotification(
                sender: $this->getUser(),
                recipient: $devis->getClient(),
                message: 'Votre devis' .' ' .$numero .' ' .'a été créé',
                document: $devis->getSlug(),
                type: 'devis'
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

    /**
     * Permet d'afficher un devis
     * 
     * @return Void
     * 
     */
    #[Route('/{slug}', name: 'app_admin_devis_show', methods: ['GET'])]
    public function show(Devis $devis): void
    {

        $mime = "application/pdf";
        $fichier = $devis->getUrl();

            header('Content-type: ' . $mime);
            readfile($fichier);
    }

    /**
     * Permet d'éditer un devis
     * 
     * @return Response
     */
    #[Route('/{id}/edit', name: 'app_admin_devis_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Devis $devis): Response
    {
        $form = $this->createForm(DevisType::class, $devis);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {


            $url = 'documents/devis/' . $devis->getSlug();
            $devis->setClient($devis->getProjet()->getClient());
            
            // On modifie le pdf
            $this->invoiceService->CreateDevis(
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
            $this->devisRepository->save($devis, true);

            $this->notificationService->addNotification(
                sender: $this->getUser(),
                recipient: $devis->getClient(),
                message: 'Votre devis' .' ' .$devis->getSlug() .' ' .'a été modfié',
                document: $devis->getSlug(),
                type: 'devis'
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