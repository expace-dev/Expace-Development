<?php

namespace App\Controller\Admin;

use DateTime;
use App\Entity\Factures;
use App\Entity\Notifications;
use App\Services\MailerService;
use App\Form\Admin\FacturesType;
use App\Services\InvoiceService;
use App\Services\NumInvoiceService;
use App\Repository\FacturesRepository;
use App\Repository\NotificationsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/factures')]
class FacturesController extends AbstractController
{
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
     * @param Request $request
     * @param NotificationsRepository $notificationsRepository
     * @param FacturesRepository $facturesRepository
     * @param NumInvoiceService $numInvoiceService
     * @param InvoiceService $invoiceService
     * @param MailerService $mailer
     * @return Response
     */
    #[Route('/new', name: 'app_admin_factures_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        NotificationsRepository $notificationsRepository, 
        FacturesRepository $facturesRepository, 
        NumInvoiceService $numInvoiceService, 
        InvoiceService $invoiceService, 
        MailerService $mailer
    ): Response
    {
        $facture = new Factures();
        $form = $this->createForm(FacturesType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $numero = $numInvoiceService->Generate(
                numInvoice: $facturesRepository->count([])+1,
                type: 'FACTURE'
            );

            
            $url = 'documents/factures/' . $numero . '.pdf';
            
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

            $mailer->sendDocument(
                from: 'noreply@expace-development.fr',
                name: 'Expace Development',
                to: $facture->getClient()->getEmail(),
                template: 'emails/_new_doc.html.twig',
                subject: 'Nouveau document',
                attache: $this->getParameter('factures_directory') . '/' . $numero .'.pdf',
                mime: 'application/pdf',
                docAttache: $numero .'.pdf',
                client: $facture->getClient()->getPrenom(),
                document: 'Facture'
            );

            $notification = new Notifications();

            $notification->setSender($this->getUser())
                         ->setRecipient($facture->getClient())
                         ->setMessage('Votre facture' .' ' .$numero .' ' .'a été créé')
                         ->setDocument('teste')
                         ->setCreatedAt(new DateTime());

            $notificationsRepository->save($notification, true);

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
     * @param Request $request
     * @param Factures $facture
     * @param NotificationsRepository $notificationsRepository
     * @param FacturesRepository $facturesRepository
     * @param NumInvoiceService $numInvoiceService
     * @param InvoiceService $invoiceService
     * @param MailerService $mailer
     * @return Response
     */
    #[Route('/{id}/edit', name: 'app_admin_factures_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, 
        Factures $facture, 
        FacturesRepository $facturesRepository, 
        NotificationsRepository $notificationsRepository, 
        InvoiceService $invoiceService, 
        MailerService $mailer
    ): Response
    {
        $form = $this->createForm(FacturesType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            
            $url = 'documents/factures/' . $facture->getSlug();
            

            $invoiceService->CreateDevis(
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

            $facturesRepository->save($facture, true);

            $mailer->sendDocument(
                from: 'noreply@expace-development.fr',
                name: 'Expace Development',
                to: $facture->getClient()->getEmail(),
                template: 'emails/_new_doc.html.twig',
                subject: 'Nouveau document',
                attache: $this->getParameter('factures_directory') . '/' . $facture->getSlug(),
                mime: 'application/pdf',
                docAttache: $facture->getSlug(),
                client: $facture->getClient()->getPrenom(),
                document: 'Facture'
            );

            $notification = new Notifications();
            $numero = substr($facture->getSlug(), 0, -4);


            $notification->setSender($this->getUser())
                         ->setRecipient($facture->getClient())
                         ->setMessage('Votre facture' .' ' .$numero .' ' .'a été modifié')
                         ->setDocument('teste')
                         ->setCreatedAt(new DateTime());

            $notificationsRepository->save($notification, true);



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
