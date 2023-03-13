<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Portfolios;
use App\Form\ContactType;
use App\Services\MailerService;
use App\Repository\PortfoliosRepository;
use App\Repository\TemoignagesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * Affiche la page d'accueil
     *
     * @param TemoignagesRepository $temoignagesRepository
     * @return Response
     */
    #[Route('/', name: 'app_home')]
    public function index(TemoignagesRepository $temoignagesRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'temoignages' => $temoignagesRepository->returnTemoignages(),
        ]);
    }

    /**
     * Permet de contacter la société
     *
     * @param MailerService $mailer
     * @param Request $request
     * @param TemoignagesRepository $temoignagesRepository
     * @return Response
     */
    #[Route('/contact', name: 'app_contact')]
    public function contact(MailerService $mailer, Request $request, TemoignagesRepository $temoignagesRepository): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

                

        if ($form->isSubmitted() && $form->isValid()) {

            $mailer->sendEmail(
                from: $contact->getEmail(), 
                name: $contact->getNom(), 
                template: 'emails/_contact.html.twig', 
                subject: $contact->getSujet(), 
                content: $contact->getMessage()
            );
            $this->addFlash('success', '<span class="me-2 fa fa-circle-check"></span>Votre message a bien été envoyé<br>Nous allons vous répondre dans les meilleurs délais');
            return $this->redirectToRoute('app_contact');
        }

        if($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash(
                'danger', 
                '<span class="me-2 fa fa-circle-exclamation"></span>Des erreurs subsistent, veuillez modifier votre saisie'
            );
        }

        return $this->render('home/contact.html.twig', [
            'temoignages' => $temoignagesRepository->returnTemoignages(),
            'form' => $form->createView(),
            'contact' => $contact
        ]);
    }

    /**
     * Permet de lister les réalisations
     *
     * @return Response
     */
    #[Route('/portfolio', name: 'app_portfolios_index')]
    public function portfolio(): Response
    {
        return $this->render('home/portfolio.html.twig');
    }

    /**
     * Permet de voir une réalisation en détails
     * 
     * @param Portfolios $portfolio
     */
    #[Route('/portfolio/details/{id}', name: 'app_portfolios_details')]
    public function portfolioDetails(Portfolios $portfolio): Response
    {

        return $this->render('home/portfolio_details.html.twig', [
            'portfolio' => $portfolio
        ]);
    }
}
