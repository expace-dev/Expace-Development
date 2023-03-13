<?php

namespace App\Controller\Admin;

use DateTime;
use App\Entity\Devis;
use App\Entity\Notifications;
use App\Form\Admin\DevisType;
use App\Services\MailerService;
use App\Services\InvoiceService;
use App\Repository\DevisRepository;
use App\Services\NumInvoiceService;
use App\Repository\NotificationsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/devis')]
class DevisController extends AbstractController
{
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
     * @param Request $request
     * @param DevisRepository $devisRepository
     * @param NotificationsRepository $notificationsRepository
     * @param InvoiceService $invoiceService
     * @param NumInvoiceService $numInvoiceService
     * @param MailerService $mailer
     * @return Response
     */
    #[Route('/new', name: 'app_admin_devis_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        DevisRepository $devisRepository, 
        NotificationsRepository $notificationsRepository, 
        InvoiceService $invoiceService, 
        NumInvoiceService $numInvoiceService, 
        MailerService $mailer
    ): Response
    {
        $devis = new Devis();
        $form = $this->createForm(DevisType::class, $devis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // On génère un numéro de devis
            $numero = $numInvoiceService->Generate(
                numInvoice: $devisRepository->count([])+1,
                type: 'DEVIS'
            );

            
            $url = 'documents/devis/' . $numero . '.pdf';
            
            $devis->setClient($devis->getProjet()->getClient());
            $slug = $numero . '.pdf';
            $devis->setSlug($slug);

            // On génère un pdf
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

            // On envoie un mail au client
            $mailer->sendDocument(
                from: 'noreply@expace-development.fr',
                name: 'Expace Development',
                to: $devis->getClient()->getEmail(),
                template: 'emails/_new_doc.html.twig',
                subject: 'Nouveau document',
                attache: $this->getParameter('devis_directory') . '/' . $slug,
                mime: 'application/pdf',
                docAttache: $slug .'.pdf',
                client: $devis->getClient()->getPrenom(),
                document: 'Devis'
            );

            // On envoie une notification au client
            $notification = new Notifications();

            $notification->setSender($this->getUser())
                         ->setRecipient($devis->getClient())
                         ->setMessage('Votre devis' .' ' .$numero .' ' .'a été créé')
                         ->setDocument('teste')
                         ->setCreatedAt(new DateTime());

            $notificationsRepository->save($notification, true);
        
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
     * @param Devis $devis
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
     * @param Request $request
     * @param Devis $devis
     * @param DevisRepository $devisRepository
     * @param NotificationsRepository $notificationsRepository
     * @param InvoiceService $invoiceService
     * @param MailerService $mailer
     * @return Response
     */
    #[Route('/{id}/edit', name: 'app_admin_devis_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, 
        Devis $devis, 
        DevisRepository $devisRepository, 
        NotificationsRepository $notificationsRepository, 
        InvoiceService $invoiceService, 
        MailerService $mailer
    ): Response
    {
        $form = $this->createForm(DevisType::class, $devis);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {


                       
            $url = 'documents/devis/' . $devis->getSlug();
            $devis->setClient($devis->getProjet()->getClient());
            
            // On modifie le pdf
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

            // On envoie un mail au client
            $mailer->sendDocument(
                from: 'noreply@expace-development.fr',
                name: 'Expace Development',
                to: $devis->getClient()->getEmail(),
                template: 'emails/_new_doc.html.twig',
                subject: 'Nouveau document',
                attache: $this->getParameter('devis_directory') . '/' . $devis->getSlug(),
                mime: 'application/pdf',
                docAttache: $devis->getSlug(),
                client: $devis->getClient()->getPrenom(),
                document: 'Devis'
            );

            // On envoie une notification au client
            $notification = new Notifications();
            $numero = substr($devis->getSlug(), 0, -4);


            $notification->setSender($this->getUser())
                         ->setRecipient($devis->getClient())
                         ->setMessage('Votre devis' .' ' .$numero .' ' .'a été modifié')
                         ->setDocument('teste')
                         ->setCreatedAt(new DateTime());

            $notificationsRepository->save($notification, true);

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
